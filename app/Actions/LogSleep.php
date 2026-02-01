<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\CalculateSleepDuration;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\ValidationException;

final readonly class LogSleep
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $bedtime,
        private ?string $wakeUpTime,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->calculateSleepDuration();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleSleep');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if ($this->bedtime === null && $this->wakeUpTime === null) {
            throw ValidationException::withMessages([
                'sleep' => 'At least one sleep value is required.',
            ]);
        }

        if ($this->bedtime !== null) {
            $this->validateTimeFormat($this->bedtime, 'Invalid bedtime format. Expected HH:MM');
        }

        if ($this->wakeUpTime !== null) {
            $this->validateTimeFormat($this->wakeUpTime, 'Invalid wake up time format. Expected HH:MM');
        }
    }

    private function validateTimeFormat(string $time, string $message): void
    {
        try {
            $parsed = Date::createFromFormat('H:i', $time);
        } catch (Exception) {
            throw ValidationException::withMessages([
                'sleep' => $message,
            ]);
        }

        if ($parsed === false || $parsed->format('H:i') !== $time) {
            throw ValidationException::withMessages([
                'sleep' => $message,
            ]);
        }
    }

    private function log(): void
    {
        $moduleSleep = $this->entry
            ->moduleSleep()
            ->firstOrCreate(
                ['journal_entry_id' => $this->entry->id],
            );

        if ($this->bedtime !== null) {
            $moduleSleep->bedtime = $this->bedtime;
        }

        if ($this->wakeUpTime !== null) {
            $moduleSleep->wake_up_time = $this->wakeUpTime;
        }

        $moduleSleep->save();
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

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }

    private function calculateSleepDuration(): void
    {
        CalculateSleepDuration::dispatch($this->entry)->onQueue('low');
    }

    private function refreshContentPresenceStatus(): void
    {
        CheckPresenceOfContentInJournalEntry::dispatch($this->entry)->onQueue('low');
    }
}
