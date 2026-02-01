<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class ToggleJournalPastEditing
{
    public function __construct(
        private User $user,
        private Journal $journal,
    ) {}

    public function execute(): Journal
    {
        $this->validate();

        $this->journal->can_edit_past = !$this->journal->can_edit_past;
        $this->journal->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();

        return $this->journal;
    }

    private function validate(): void
    {
        if ($this->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        $rawValue = $this->journal->getAttributes()['can_edit_past'] ?? null;

        if (!in_array($rawValue, [0, 1, true, false], true)) {
            throw ValidationException::withMessages([
                'can_edit_past' => 'Editing past entries must be boolean.',
            ]);
        }
    }

    private function logUserAction(): void
    {
        $state = $this->journal->can_edit_past ? 'enabled' : 'disabled';

        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->journal,
            action: 'journal_past_editing_toggled',
            description: sprintf('Past entry editing %s', $state),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
