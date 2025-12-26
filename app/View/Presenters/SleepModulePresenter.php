<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Helpers\Modules\SleepHelper;
use App\Models\JournalEntry;

final readonly class SleepModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(string $bedtime, string $wake_up_time): array
    {
        $bedtimeRange = SleepHelper::range($bedtime, $this->entry?->bedtime);
        $wakeUpRange = SleepHelper::range($wake_up_time, $this->entry?->wake_up_time);

        $previousBedtimeUrl = route('journal.entry.sleep.show', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
            'bedtime' => SleepHelper::shift($bedtime, -5),
            'wake_up_time' => $wake_up_time,
        ]);

        $nextBedtimeUrl = route('journal.entry.sleep.show', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
            'bedtime' => SleepHelper::shift($bedtime, +5),
            'wake_up_time' => $wake_up_time,
        ]);

        $previousWakeUpUrl = route('journal.entry.sleep.show', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
            'bedtime' => $bedtime,
            'wake_up_time' => SleepHelper::shift($wake_up_time, -5),
        ]);

        $nextWakeUpUrl = route('journal.entry.sleep.show', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
            'bedtime' => $bedtime,
            'wake_up_time' => SleepHelper::shift($wake_up_time, +5),
        ]);

        return [
            'bedtime' => $bedtimeRange,
            'wake_up_time' => $wakeUpRange,
            'previous_bedtime_url' => $previousBedtimeUrl,
            'next_bedtime_url' => $nextBedtimeUrl,
            'previous_wake_up_url' => $previousWakeUpUrl,
            'next_wake_up_url' => $nextWakeUpUrl,
        ];
    }
}
