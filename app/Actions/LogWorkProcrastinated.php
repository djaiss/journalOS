<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

/**
 * This action logs whether the user has procrastinated or not in this day.
 */
final readonly class LogWorkProcrastinated
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $workProcrastinated,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $moduleWork = $this->entry->moduleWork()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleWork->work_procrastinated = $this->workProcrastinated;
        $moduleWork->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleWork');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
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
