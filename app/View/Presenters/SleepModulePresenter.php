<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Helpers\Modules\SleepHelper;
use App\Models\JournalEntry;
use Carbon\CarbonImmutable;

final readonly class SleepModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(
        string $bedtime = '20:00',
        string $wake_up_time = '06:00',
        bool $skipShift = false,
    ): array {
        $moduleSleep = $this->entry->moduleSleep;

        if (! $skipShift) {
            $bedtime = $this->shiftIfValid($moduleSleep?->bedtime, -2) ?? $bedtime;
            $wake_up_time = $this->shiftIfValid($moduleSleep?->wake_up_time, -2) ?? $wake_up_time;
        }

        $bedtimeRange = SleepHelper::range($bedtime, $moduleSleep?->bedtime);
        $wakeUpRange = SleepHelper::range($wake_up_time, $moduleSleep?->wake_up_time);

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

        $bedtimeUpdateUrl = route('journal.entry.sleep.bedtime.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $wakeUpTimeUpdateUrl = route('journal.entry.sleep.wake_up_time.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $resetUrl = route('journal.entry.sleep.reset', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        return [
            'bedtime' => $bedtimeRange,
            'wake_up_time' => $wakeUpRange,
            'previous_bedtime_url' => $previousBedtimeUrl,
            'next_bedtime_url' => $nextBedtimeUrl,
            'previous_wake_up_url' => $previousWakeUpUrl,
            'next_wake_up_url' => $nextWakeUpUrl,
            'bedtime_update_url' => $bedtimeUpdateUrl,
            'wake_up_time_update_url' => $wakeUpTimeUpdateUrl,
            'reset_url' => $resetUrl,
            'display_reset' => ! is_null($moduleSleep?->bedtime) || ! is_null($moduleSleep?->wake_up_time),
        ];
    }

    private function shiftIfValid(?string $time, int $hours): ?string
    {
        $time = is_string($time) ? mb_trim($time) : null;

        if ($time === null || $time === '') {
            return null;
        }

        $dt = CarbonImmutable::createFromFormat('H:i', $time);
        if (! $dt instanceof CarbonImmutable) {
            return null;
        }

        return $dt->addHours($hours)->format('H:i');
    }
}
