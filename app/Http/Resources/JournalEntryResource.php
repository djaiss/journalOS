<?php

declare(strict_types = 1);

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
        $richTextNotes = $this->richTextNotes;
        $notes = $richTextNotes ? mb_trim($richTextNotes->render()) : null;
        $notes = $richTextNotes && $richTextNotes->toPlainText() === '' ? null : $notes;

        return [
            'type' => 'journal_entry',
            'id' => (string) $this->id,
            'attributes' => [
                'journal_id' => $this->journal_id,
                'day' => $this->day,
                'month' => $this->month,
                'year' => $this->year,
                'notes' => $notes,
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
                    'weather' => [
                        'condition' => $this->moduleWeather?->condition,
                        'temperature_range' => $this->moduleWeather?->temperature_range,
                        'precipitation' => $this->moduleWeather?->precipitation,
                        'daylight' => $this->moduleWeather?->daylight,
                    ],
                    'weather_influence' => [
                        'mood_effect' => $this->moduleWeatherInfluence?->mood_effect,
                        'energy_effect' => $this->moduleWeatherInfluence?->energy_effect,
                        'plans_influence' => $this->moduleWeatherInfluence?->plans_influence,
                        'outside_time' => $this->moduleWeatherInfluence?->outside_time,
                    ],
                    'shopping' => [
                        'has_shopped_today' => $this->moduleShopping?->has_shopped_today,
                        'shopping_type' => $this->moduleShopping?->shopping_type,
                        'shopping_intent' => $this->moduleShopping?->shopping_intent,
                        'shopping_context' => $this->moduleShopping?->shopping_context,
                        'shopping_for' => $this->moduleShopping?->shopping_for,
                    ],
                    'meals' => [
                        'meal_presence' => $this->moduleMeals?->meal_presence,
                        'meal_type' => $this->moduleMeals?->meal_type,
                        'social_context' => $this->moduleMeals?->social_context,
                        'has_notes' => $this->moduleMeals?->has_notes,
                        'notes' => $this->moduleMeals?->notes,
                    ],
                    'kids' => [
                        'had_kids_today' => $this->moduleKids?->had_kids_today,
                    ],
                    'day_type' => [
                        'day_type' => $this->moduleDayType?->day_type,
                    ],
                    'primary_obligation' => [
                        'primary_obligation' => $this->modulePrimaryObligation?->primary_obligation,
                    ],
                    'physical_activity' => [
                        'has_done_physical_activity' => $this->modulePhysicalActivity?->has_done_physical_activity,
                        'activity_type' => $this->modulePhysicalActivity?->activity_type,
                        'activity_intensity' => $this->modulePhysicalActivity?->activity_intensity,
                    ],
                    'health' => [
                        'health' => $this->moduleHealth?->health,
                    ],
                    'hygiene' => [
                        'showered' => $this->moduleHygiene?->showered,
                        'brushed_teeth' => $this->moduleHygiene?->brushed_teeth,
                        'skincare' => $this->moduleHygiene?->skincare,
                    ],
                    'mood' => [
                        'mood' => $this->moduleMood?->mood,
                    ],
                    'reading' => [
                        'did_read_today' => $this->moduleReading?->did_read_today,
                        'books' => $this->books->map(fn ($book) => [
                            'id' => $book->id,
                            'name' => $book->name,
                            'status' => $book->pivot?->status,
                        ])->all(),
                        'reading_amount' => $this->moduleReading?->reading_amount,
                        'mental_state' => $this->moduleReading?->mental_state,
                        'reading_feel' => $this->moduleReading?->reading_feel,
                        'want_continue' => $this->moduleReading?->want_continue,
                        'reading_limit' => $this->moduleReading?->reading_limit,
                    ],
                    'sexual_activity' => [
                        'had_sexual_activity' => $this->moduleSexualActivity?->had_sexual_activity,
                        'sexual_activity_type' => $this->moduleSexualActivity?->sexual_activity_type,
                    ],
                    'energy' => [
                        'energy' => $this->moduleEnergy?->energy,
                    ],
                    'cognitive_load' => [
                        'cognitive_load' => $this->moduleCognitiveLoad?->cognitive_load,
                        'primary_source' => $this->moduleCognitiveLoad?->primary_source,
                        'load_quality' => $this->moduleCognitiveLoad?->load_quality,
                    ],
                    'social_density' => [
                        'social_density' => $this->moduleSocialDensity?->social_density,
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
