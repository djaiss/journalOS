<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Date;

/**
 * Log sleep habits for a journal entry.
 * Time is set using 24-hour format (HH:MM) and represented as a string.
 */
final readonly class LogSleep
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $bedtime,
        private string $wakeUpTime,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->create();
        $this->updateUserLastActivityDate();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        try {
            $bedtime = Date::createFromFormat('H:i', $this->bedtime);
        } catch (Exception) {
            throw new Exception('Invalid bedtime format. Expected HH:MM');
        }
        if ($bedtime === false || $bedtime->format('H:i') !== $this->bedtime) {
            throw new Exception('Invalid bedtime format. Expected HH:MM');
        }

        try {
            $wakeUp = Date::createFromFormat('H:i', $this->wakeUpTime);
        } catch (Exception) {
            throw new Exception('Invalid wake-up time format. Expected HH:MM');
        }
        if ($wakeUp === false || $wakeUp->format('H:i') !== $this->wakeUpTime) {
            throw new Exception('Invalid wake-up time format. Expected HH:MM');
        }
    }

    private function create(): void
    {
        $bedtime = Date::createFromFormat('H:i', $this->bedtime);
        $wakeUp = Date::createFromFormat('H:i', $this->wakeUpTime);

        if ($wakeUp->lessThanOrEqualTo($bedtime)) {
            $wakeUp->addDay();
        }

        $sleepDuration = $bedtime->diff($wakeUp)->format('%H:%I');

        $this->entry->bedtime = $this->bedtime;
        $this->entry->wake_up_time = $this->wakeUpTime;
        $this->entry->sleep_duration = $sleepDuration;
        $this->entry->save();

        $this->logUserAction();
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'sleep_logged',
            description: 'Logged sleep for journal entry on ' . $this->entry->getDate(),
        )->onQueue('low');
    }
}
