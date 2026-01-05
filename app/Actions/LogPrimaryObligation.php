<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Helpers\TextSanitizer;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final class LogPrimaryObligation
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $primaryObligation,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->primary_obligation = $this->primaryObligation;
        $this->entry->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->primaryObligation = TextSanitizer::plainText($this->primaryObligation);

        $messages = [];

        if ($this->primaryObligation === '') {
            $messages['primary_obligation'] = 'Primary obligation must be plain text.';
        }

        if (mb_strlen($this->primaryObligation) > 255) {
            $messages['primary_obligation'] = 'Primary obligation must not be longer than 255 characters.';
        }

        if ($messages === []) {
            $validObligationValues = ['work', 'family', 'personal', 'health', 'travel', 'none'];
            if (! in_array($this->primaryObligation, $validObligationValues, true)) {
                $messages['primary_obligation'] = 'Invalid primary obligation value.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages([
                'primary_obligation' => $messages['primary_obligation'],
            ]);
        }
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
