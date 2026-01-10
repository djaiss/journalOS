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
 * This action logs whether the user has traveled or not in this day.
 */
final readonly class LogTravelledToday
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $hasTraveled,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleTravel');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        if ($this->hasTraveled !== 'yes' && $this->hasTraveled !== 'no') {
            throw new InvalidArgumentException('hasTraveled must be either "yes" or "no"');
        }
    }

    private function log(): void
    {
        $moduleTravel = $this->entry->moduleTravel()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleTravel->has_traveled_today = $this->hasTraveled;
        $moduleTravel->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'has_traveled_logged',
            description: 'Logged if you have traveled on ' . $this->entry->getDate(),
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
