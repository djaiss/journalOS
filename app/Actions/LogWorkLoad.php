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
 * This action logs the work load for the user in this day.
 */
final readonly class LogWorkLoad
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $workLoad,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
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

        if (! in_array($this->workLoad, ['light', 'medium', 'heavy'])) {
            throw new InvalidArgumentException('workLoad must be either "light", "medium", or "heavy"');
        }
    }

    private function log(): void
    {
        $moduleWork = $this->entry->moduleWork()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleWork->work_load = $this->workLoad;
        $moduleWork->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'work_load_logged',
            description: 'Logged work load on ' . $this->entry->getDate(),
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
