<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CalculateSleepDuration;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Helpers\TextSanitizer;
use App\Models\JournalEntry;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Date;

final class LogBedTime
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $bedtime,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->entry->bedtime = $this->bedtime;
        $this->entry->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->calculateSleepDuration();
        $this->refreshContentPresenceStatus();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        $this->bedtime = TextSanitizer::plainText($this->bedtime);

        if ($this->bedtime === '' || mb_strlen($this->bedtime) !== 5) {
            throw new Exception('Invalid bedtime format. Expected HH:MM');
        }

        $this->validateTimeFormat($this->bedtime, 'Invalid bedtime format. Expected HH:MM');
    }

    private function validateTimeFormat(string $time, string $message): void
    {
        try {
            $parsed = Date::createFromFormat('H:i', $time);
        } catch (Exception) {
            throw new Exception($message);
        }

        if ($parsed === false || $parsed->format('H:i') !== $time) {
            throw new Exception($message);
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'sleep_bedtime_logged',
            description: 'Logged bedtime for journal entry on ' . $this->entry->getDate(),
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
