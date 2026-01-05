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
 * This action logs whether the user has procrastinated or not in this day.
 */
final class LogWorkProcrastinated
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $workProcrastinated,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->entry->work_procrastinated = $this->workProcrastinated;
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

        $this->workProcrastinated = TextSanitizer::plainText($this->workProcrastinated);

        if ($this->workProcrastinated === '') {
            throw new InvalidArgumentException('workProcrastinated must be plain text');
        }

        if (mb_strlen($this->workProcrastinated) > 255) {
            throw new InvalidArgumentException('workProcrastinated must not be longer than 255 characters');
        }

        if ($this->workProcrastinated !== 'yes' && $this->workProcrastinated !== 'no') {
            throw new InvalidArgumentException('workProcrastinated must be either "yes" or "no"');
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'work_procrastinated_logged',
            description: 'Logged if you have procrastinated on ' . $this->entry->getDate(),
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
