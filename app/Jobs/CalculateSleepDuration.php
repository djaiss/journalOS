<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\JournalEntry;
use App\Models\ModuleSleep;
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
        $moduleSleep = $this->entry->moduleSleep;

        if ($moduleSleep === null || $moduleSleep->bedtime === null || $moduleSleep->wake_up_time === null) {
            return;
        }

        $moduleSleep->sleep_duration_in_minutes = $this->calculateSleepDuration($moduleSleep);
        $moduleSleep->save();
    }

    private function calculateSleepDuration(ModuleSleep $moduleSleep): string
    {
        $bedtime = Date::createFromFormat('H:i', $moduleSleep->bedtime);
        $wakeUpTime = Date::createFromFormat('H:i', $moduleSleep->wake_up_time);

        // If wake up time is earlier than bedtime, it means we crossed midnight
        if ($wakeUpTime < $bedtime) {
            $wakeUpTime = $wakeUpTime->addDay();
        }

        return (string) $wakeUpTime->diffInMinutes($bedtime, true);
    }
}
