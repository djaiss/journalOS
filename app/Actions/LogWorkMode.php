<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

/**
 * This action logs the work mode for the user in this day.
 */
final readonly class LogWorkMode
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $workMode,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->entry->work_mode = $this->workMode;
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

        if (! in_array($this->workMode, ['on-site', 'remote', 'hybrid'])) {
            throw new InvalidArgumentException('workMode must be either "on-site", "remote", or "hybrid"');
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'work_mode_logged',
            description: 'Logged work mode on ' . $this->entry->getDate(),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
