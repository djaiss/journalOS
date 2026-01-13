<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\DeleteRelatedJournalData;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DestroyJournal
{
    private string $journalName;
    private int $journalId;

    public function __construct(
        private User $user,
        private Journal $journal,
    ) {
        $this->journalId = $this->journal->id;
        $this->journalName = $this->journal->name;
    }

    public function execute(): void
    {
        $this->validate();
        $this->deleteRelatedData();
        $this->delete();
        $this->log();
        $this->updateUserLastActivityDate();
    }

    private function validate(): void
    {
        if ($this->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }
    }

    private function delete(): void
    {
        $this->journal->delete();
    }

    private function deleteRelatedData(): void
    {
        DeleteRelatedJournalData::dispatch($this->journalId)->onQueue('low');
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'journal_deletion',
            description: sprintf('Deleted the journal called %s', $this->journalName),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
