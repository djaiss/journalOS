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

final readonly class LogShoppingFor
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $shoppingFor,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleShopping');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $validTargets = ['for_self', 'for_household', 'for_others'];
        if (! in_array($this->shoppingFor, $validTargets, true)) {
            throw ValidationException::withMessages([
                'shopping_for' => 'Invalid shopping for value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleShopping = $this->entry->moduleShopping()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleShopping->shopping_for = $this->shoppingFor;
        $moduleShopping->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'shopping_for_logged',
            description: 'Logged shopping target for ' . $this->entry->getDate(),
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
