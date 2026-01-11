<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class JournalEntryPresenter
{
    public function __construct(private JournalEntry $entry) {}

    public function build(): array
    {
        if ($this->entry->journal->show_sleep_module) {
            $sleep = new SleepModulePresenter($this->entry)->build('20:00', '06:00');
        } else {
            $sleep = [];
        }

        if ($this->entry->journal->show_work_module) {
            $work = new WorkModulePresenter($this->entry)->build();
        } else {
            $work = [];
        }

        if ($this->entry->journal->show_travel_module) {
            $travel = new TravelModulePresenter($this->entry)->build();
        } else {
            $travel = [];
        }

        if ($this->entry->journal->show_shopping_module) {
            $shopping = new ShoppingModulePresenter($this->entry)->build();
        } else {
            $shopping = [];
        }

        if ($this->entry->journal->show_kids_module) {
            $kids = new KidsModulePresenter($this->entry)->build();
        } else {
            $kids = [];
        }

        if ($this->entry->journal->show_day_type_module) {
            $dayType = new DayTypeModulePresenter($this->entry)->build();
        } else {
            $dayType = [];
        }

        if ($this->entry->journal->show_primary_obligation_module) {
            $primaryObligation = new PrimaryObligationModulePresenter($this->entry)->build();
        } else {
            $primaryObligation = [];
        }

        if ($this->entry->journal->show_physical_activity_module) {
            $physicalActivity = new PhysicalActivityModulePresenter($this->entry)->build();
        } else {
            $physicalActivity = [];
        }

        if ($this->entry->journal->show_health_module) {
            $health = new HealthModulePresenter($this->entry)->build();
        } else {
            $health = [];
        }

        if ($this->entry->journal->show_hygiene_module) {
            $hygiene = new HygieneModulePresenter($this->entry)->build();
        } else {
            $hygiene = [];
        }

        if ($this->entry->journal->show_mood_module) {
            $mood = new MoodModulePresenter($this->entry)->build();
        } else {
            $mood = [];
        }

        if ($this->entry->journal->show_sexual_activity_module) {
            $sexualActivity = new SexualActivityModulePresenter($this->entry)->build();
        } else {
            $sexualActivity = [];
        }

        if ($this->entry->journal->show_energy_module) {
            $energy = new EnergyModulePresenter($this->entry)->build();
        } else {
            $energy = [];
        }

        if ($this->entry->journal->show_social_density_module) {
            $socialDensity = new SocialDensityModulePresenter($this->entry)->build();
        } else {
            $socialDensity = [];
        }

        $notes = new NotesPresenter($this->entry)->build();

        return [
            'sleep' => $sleep,
            'work' => $work,
            'travel' => $travel,
            'shopping' => $shopping,
            'kids' => $kids,
            'day_type' => $dayType,
            'primary_obligation' => $primaryObligation,
            'physical_activity' => $physicalActivity,
            'health' => $health,
            'hygiene' => $hygiene,
            'mood' => $mood,
            'sexual_activity' => $sexualActivity,
            'energy' => $energy,
            'social_density' => $socialDensity,
            'notes' => $notes,
        ];
    }
}
