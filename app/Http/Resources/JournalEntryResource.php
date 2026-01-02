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
                        'bedtime' => $this->bedtime,
                        'wake_up_time' => $this->wake_up_time,
                        'sleep_duration_in_minutes' => $this->sleep_duration_in_minutes,
                    ],
                    'work' => [
                        'worked' => $this->worked,
                        'work_mode' => $this->work_mode,
                        'work_load' => $this->work_load,
                        'work_procrastinated' => $this->work_procrastinated,
                    ],
                    'travel' => [
                        'has_traveled_today' => $this->has_traveled_today,
                        'travel_mode' => $this->travel_mode,
                    ],
                    'day_type' => [
                        'day_type' => $this->day_type,
                    ],
                    'physical_activity' => [
                        'has_done_physical_activity' => $this->has_done_physical_activity,
                        'activity_type' => $this->activity_type,
                        'activity_intensity' => $this->activity_intensity,
                    ],
                    'health' => [
                        'health' => $this->health,
                    ],
                    'mood' => [
                        'mood' => $this->mood,
                    ],
                    'energy' => [
                        'energy' => $this->energy,
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
