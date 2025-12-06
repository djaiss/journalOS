<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Account;
use App\Models\Journal;
use App\Models\User;

final class PruneAccount
{
    public function __construct(
        public User $user,
    ) {}

    /**
     * Prune the account by deleting all journals and related data.
     */
    public function execute(): void
    {
        $this->deleteJournals();
        $this->updateUserLastActivityDate();
        $this->logUserAction();
    }

    private function deleteJournals(): void
    {
        Journal::where('user_id', $this->user->id)->delete();
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'account_pruning',
            description: 'Deleted all journals and related data from your account',
        )->onQueue('low');
    }
}
