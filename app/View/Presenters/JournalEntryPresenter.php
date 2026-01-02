<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class JournalEntryPresenter
{
    public function __construct(private JournalEntry $entry) {}

    public function build(): array
    {
        $sleep = new SleepModulePresenter($this->entry)
            ->build('20:00', '06:00');

        $work = new WorkModulePresenter($this->entry)->build();

        $travel = new TravelModulePresenter($this->entry)->build();

        $dayType = new DayTypeModulePresenter($this->entry)->build();

        $physicalActivity = new PhysicalActivityModulePresenter($this->entry)->build();

        $health = new HealthModulePresenter($this->entry)->build();

        $mood = new MoodModulePresenter($this->entry)->build();

        $energy = new EnergyModulePresenter($this->entry)->build();

        return [
            'sleep' => $sleep,
            'work' => $work,
            'travel' => $travel,
            'day_type' => $dayType,
            'physical_activity' => $physicalActivity,
            'health' => $health,
            'mood' => $mood,
            'energy' => $energy,
        ];
    }
}
