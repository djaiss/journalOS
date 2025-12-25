<?php

declare(strict_types=1);

namespace App\Http\ViewModels\Journal;

use App\Helpers\TimeHelper;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final readonly class ModuleSleepViewModel
{
    public function __construct(
        private JournalEntry $journalEntry,
    ) {}

    public function sleep(string $startBedTime, string $startWakeUpTime): array
    {
        $bedtime = $this->journalEntry->bedtime ? TimeHelper::format($this->journalEntry->bedtime) : null;
        $wakeUpTime = $this->journalEntry->wake_up_time ? TimeHelper::format($this->journalEntry->wake_up_time) : null;

        return [
            'bedtime' => $this->getRange($startBedTime, $bedtime),
            'next_bedtime_url' => route('journal.entry.sleep.show', [
                'slug' => $this->journalEntry->journal->slug,
                'year' => $this->journalEntry->year,
                'month' => $this->journalEntry->month,
                'day' => $this->journalEntry->day,
                'bedtime' => \Illuminate\Support\Facades\Date::createFromFormat('H:i', $startBedTime)->addHour(5)->format('H:i'),
                'wake_up_time' => $startWakeUpTime,
            ]),
            'previous_bedtime_url' => route('journal.entry.sleep.show', [
                'slug' => $this->journalEntry->journal->slug,
                'year' => $this->journalEntry->year,
                'month' => $this->journalEntry->month,
                'day' => $this->journalEntry->day,
                'bedtime' => \Illuminate\Support\Facades\Date::createFromFormat('H:i', $startBedTime)->subHour(5)->format('H:i'),
                'wake_up_time' => $startWakeUpTime,
            ]),
            'wake_up_time' => $this->getRange($startWakeUpTime, $wakeUpTime),
            'next_wake_up_time_url' => route('journal.entry.sleep.show', [
                'slug' => $this->journalEntry->journal->slug,
                'year' => $this->journalEntry->year,
                'month' => $this->journalEntry->month,
                'day' => $this->journalEntry->day,
                'bedtime' => $startBedTime,
                'wake_up_time' => \Illuminate\Support\Facades\Date::createFromFormat('H:i', $startWakeUpTime)->addHour(5)->format('H:i'),
            ]),
            'previous_wake_up_time_url' => route('journal.entry.sleep.show', [
                'slug' => $this->journalEntry->journal->slug,
                'year' => $this->journalEntry->year,
                'month' => $this->journalEntry->month,
                'day' => $this->journalEntry->day,
                'bedtime' => $startBedTime,
                'wake_up_time' => \Illuminate\Support\Facades\Date::createFromFormat('H:i', $startWakeUpTime)->subHour(5)->format('H:i'),
            ]),
        ];
    }

    public function getRange(string $start, ?string $valueToMatch): Collection
    {
        $start = \Illuminate\Support\Facades\Date::createFromFormat('H:i', $start);

        $options = collect();
        for ($i = 0; $i < 5; $i++) {
            $time = $start->copy()->addHour();
            $options->push([
                'time' => $time->format('H:i'),
                'formatted' => TimeHelper::format($time->format('H:i')),
                'is_selected' => $valueToMatch === TimeHelper::format($time->format('H:i')),
            ]);
        }

        return $options;
    }
}
