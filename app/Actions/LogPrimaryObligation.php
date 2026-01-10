<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogPrimaryObligation
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $primaryObligation,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('modulePrimaryObligation');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $validObligationValues = ['work', 'family', 'personal', 'health', 'travel', 'none'];
        if (! in_array($this->primaryObligation, $validObligationValues, true)) {
            throw ValidationException::withMessages([
                'primary_obligation' => 'Invalid primary obligation value.',
            ]);
        }
    }

    private function log(): void
    {
        $modulePrimaryObligation = $this->entry->modulePrimaryObligation()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $modulePrimaryObligation->primary_obligation = $this->primaryObligation;
        $modulePrimaryObligation->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'primary_obligation_logged',
            description: 'Logged primary obligation for ' . $this->entry->getDate(),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }

    private function refreshContentPresenceStatus(): void
    {
        CheckPresenceOfContentInJournalEntry::dispatch($this->entry)->onQueue('low');
    }
}
