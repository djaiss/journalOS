<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\Date;

/**
 * Calculate the sleep duration of the given journal entry.
 * It can only be calculated if the bedtime and wake up time are set.
 */
final class CalculateSleepDuration implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JournalEntry $entry,
    ) {}

    public function handle(): void
    {
        if ($this->entry->bedtime === null || $this->entry->wake_up_time === null) {
            return;
        }

        $this->entry->sleep_duration_in_minutes = $this->calculateSleepDuration();
        $this->entry->save();
    }

    private function calculateSleepDuration(): string
    {
        $bedtime = Date::createFromFormat('H:i', $this->entry->bedtime);
        $wake_up_time = Date::createFromFormat('H:i', $this->entry->wake_up_time);
        return (string) $wake_up_time->diffInMinutes($bedtime);
    }
}
