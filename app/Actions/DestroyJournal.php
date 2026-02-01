<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use App\Models\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DestroyJournal
{
    private string $journalName;

    public function __construct(
        private User $user,
        private Journal $journal,
    ) {
        $this->journalName = $this->journal->name;
    }

    public function execute(): void
    {
        $this->validate();
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
        Log::where('journal_id', $this->journal->id)->update(['journal_id' => null]);
        $this->journal->delete();
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
