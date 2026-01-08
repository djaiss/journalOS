<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin JournalEntry
 */
final class JournalEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'journal_entry',
            'id' => (string) $this->id,
            'attributes' => [
                'journal_id' => $this->journal_id,
                'day' => $this->day,
                'month' => $this->month,
                'year' => $this->year,
                'modules' => [
                    'sleep' => [
                        'bedtime' => $this->moduleSleep?->bedtime,
                        'wake_up_time' => $this->moduleSleep?->wake_up_time,
                        'sleep_duration_in_minutes' => $this->moduleSleep?->sleep_duration_in_minutes,
                    ],
                    'work' => [
                        'worked' => $this->moduleWork?->worked,
                        'work_mode' => $this->moduleWork?->work_mode,
                        'work_load' => $this->moduleWork?->work_load,
                        'work_procrastinated' => $this->moduleWork?->work_procrastinated,
                    ],
                    'travel' => [
                        'has_traveled_today' => $this->moduleTravel?->has_traveled_today,
                        'travel_mode' => $this->moduleTravel?->travel_mode,
                    ],
                    'kids' => [
                        'had_kids_today' => $this->had_kids_today,
                    ],
                    'day_type' => [
                        'day_type' => $this->moduleDayType?->day_type,
                    ],
                    'primary_obligation' => [
                        'primary_obligation' => $this->primary_obligation,
                    ],
                    'physical_activity' => [
                        'has_done_physical_activity' => $this->modulePhysicalActivity?->has_done_physical_activity,
                        'activity_type' => $this->modulePhysicalActivity?->activity_type,
                        'activity_intensity' => $this->modulePhysicalActivity?->activity_intensity,
                    ],
                    'health' => [
                        'health' => $this->moduleHealth?->health,
                    ],
                    'mood' => [
                        'mood' => $this->moduleMood?->mood,
                    ],
                    'sexual_activity' => [
                        'had_sexual_activity' => $this->moduleSexualActivity?->had_sexual_activity,
                        'sexual_activity_type' => $this->moduleSexualActivity?->sexual_activity_type,
                    ],
                    'energy' => [
                        'energy' => $this->moduleEnergy?->energy,
                    ],
                    'social_density' => [
                        'social_density' => $this->social_density,
                    ],
                ],
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.journal.entry.show', [
                    'id' => $this->journal_id,
                    'year' => $this->year,
                    'month' => $this->month,
                    'day' => $this->day,
                ]),
            ],
        ];
    }
}
