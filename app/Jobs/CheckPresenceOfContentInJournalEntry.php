<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\JournalEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Check if the journal entry has any content in it.
 * If there is, it sets the has_content field to true, otherwise to false.
 * This job is triggered once any action is done on a journal entry.
 */
final class CheckPresenceOfContentInJournalEntry implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JournalEntry $entry,
    ) {}

    public function handle(): void
    {
        $this->entry->loadMissing([
            'moduleEnergy',
            'moduleKids',
            'modulePhysicalActivity',
            'modulePrimaryObligation',
            'moduleSocialDensity',
            'moduleMood',
            'moduleSleep',
            'moduleWork',
            'moduleSexualActivity',
            'moduleHealth',
            'moduleHygiene',
            'moduleDayType',
            'moduleTravel',
            'moduleWeather',
            'moduleShopping',
        ]);

        $hasContent = false;

        if ($this->entry->moduleSleep !== null) {
            $moduleSleep = $this->entry->moduleSleep;
            if ($moduleSleep->bedtime !== null || $moduleSleep->wake_up_time !== null || $moduleSleep->sleep_duration_in_minutes !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleMood !== null) {
            if ($this->entry->moduleMood->mood !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleWork !== null) {
            $moduleWork = $this->entry->moduleWork;
            if ($moduleWork->worked !== null || $moduleWork->work_mode !== null || $moduleWork->work_load !== null || $moduleWork->work_procrastinated !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->modulePhysicalActivity !== null) {
            $modulePhysicalActivity = $this->entry->modulePhysicalActivity;
            if ($modulePhysicalActivity->has_done_physical_activity !== null || $modulePhysicalActivity->activity_type !== null || $modulePhysicalActivity->activity_intensity !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleEnergy !== null) {
            if ($this->entry->moduleEnergy->energy !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleKids !== null) {
            if ($this->entry->moduleKids->had_kids_today !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->modulePrimaryObligation !== null) {
            if ($this->entry->modulePrimaryObligation->primary_obligation !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleSocialDensity !== null) {
            if ($this->entry->moduleSocialDensity->social_density !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleSexualActivity !== null) {
            $moduleSexualActivity = $this->entry->moduleSexualActivity;
            if ($moduleSexualActivity->had_sexual_activity !== null || $moduleSexualActivity->sexual_activity_type !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleHealth !== null) {
            if ($this->entry->moduleHealth->health !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleHygiene !== null) {
            $moduleHygiene = $this->entry->moduleHygiene;
            if ($moduleHygiene->showered !== null || $moduleHygiene->brushed_teeth !== null || $moduleHygiene->skincare !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleDayType !== null) {
            if ($this->entry->moduleDayType->day_type !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleTravel !== null) {
            $moduleTravel = $this->entry->moduleTravel;
            if ($moduleTravel->has_traveled_today !== null || $moduleTravel->travel_details !== null || $moduleTravel->travel_mode !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleWeather !== null) {
            $moduleWeather = $this->entry->moduleWeather;
            if ($moduleWeather->condition !== null
                || $moduleWeather->temperature_range !== null
                || $moduleWeather->precipitation !== null
                || $moduleWeather->daylight !== null
            ) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleShopping !== null) {
            $moduleShopping = $this->entry->moduleShopping;
            if ($moduleShopping->has_shopped_today !== null || $moduleShopping->shopping_type !== null || $moduleShopping->shopping_intent !== null || $moduleShopping->shopping_context !== null || $moduleShopping->shopping_for !== null) {
                $hasContent = true;
            }
        }

        $richTextNotes = $this->entry->richTextNotes;
        if (! $hasContent && $richTextNotes !== null && ! $richTextNotes->isEmpty()) {
            $hasContent = true;
        }

        $this->entry->has_content = $hasContent;
        $this->entry->save();
    }
}
