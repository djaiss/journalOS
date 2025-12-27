<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class ResetSleepData
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->entry->bedtime = null;
        $this->entry->wake_up_time = null;
        $this->entry->sleep_duration_in_minutes = null;
        $this->entry->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'sleep_data_reset',
            description: 'Reset sleep data for journal entry on ' . $this->entry->getDate(),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
