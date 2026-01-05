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
use InvalidArgumentException;

/**
 * This action logs the work mode for the user in this day.
 */
final class LogWorkMode
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
        $this->refreshContentPresenceStatus();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        $this->workMode = TextSanitizer::plainText($this->workMode);

        if ($this->workMode === '') {
            throw new InvalidArgumentException('workMode must be plain text');
        }

        if (mb_strlen($this->workMode) > 255) {
            throw new InvalidArgumentException('workMode must not be longer than 255 characters');
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

    private function refreshContentPresenceStatus(): void
    {
        CheckPresenceOfContentInJournalEntry::dispatch($this->entry)->onQueue('low');
    }
}
