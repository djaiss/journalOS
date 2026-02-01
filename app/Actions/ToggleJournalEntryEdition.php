<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class ToggleJournalEntryEdition
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->is_edited = !$this->entry->is_edited;
        $this->entry->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();

        return $this->entry->fresh();
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        $rawValue = $this->entry->getAttributes()['is_edited'] ?? null;

        if (!in_array($rawValue, [0, 1, true, false], true)) {
            throw ValidationException::withMessages([
                'is_edited' => 'Edited state must be boolean.',
            ]);
        }
    }

    private function logUserAction(): void
    {
        $state = $this->entry->is_edited ? 'enabled' : 'disabled';

        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'journal_entry_edition_toggled',
            description: sprintf('Entry edition %s for %s', $state, $this->entry->getDate()),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
