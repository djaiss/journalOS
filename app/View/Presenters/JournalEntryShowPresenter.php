<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Actions\GetJournalEntryMarkdownForLLM;
use App\Helpers\ModuleCatalog;
use App\Helpers\TextSanitizer;
use App\Models\JournalEntry;
use App\Models\Layout;
use Illuminate\Support\Str;

final readonly class JournalEntryShowPresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    /**
     * @return array{
     *   date: string,
     *   notes_markdown: string|null,
     *   modules: array<int, array{
     *     key: string,
     *     emoji: string,
     *     title: string,
     *     rows: array<int, array{label: string, value: string|array<int, string>}>
     *   }>
     * }
     */
    public function build(): array
    {
        $layout = $this->resolveLayout();
        $moduleKeys = GetJournalEntryMarkdownForLLM::moduleKeysForLayout($layout);

        $this->entry->loadMissing(array_merge(
            GetJournalEntryMarkdownForLLM::relationshipsForModules($moduleKeys),
            ['richTextNotes'],
        ));

        return [
            'date' => $this->entry->getDate(),
            'notes_markdown' => $this->notesMarkdown(),
            'modules' => $this->buildModules($moduleKeys),
        ];
    }

    /**
     * @param  array<int, string>  $moduleKeys
     * @return array<int, array{
     *   key: string,
     *   emoji: string,
     *   title: string,
     *   rows: array<int, array{label: string, value: string|array<int, string>}>
     * }>
     */
    private function buildModules(array $moduleKeys): array
    {
        $modules = [];

        foreach ($moduleKeys as $moduleKey) {
            $definition = $this->moduleDefinition($moduleKey);

            if ($definition === null) {
                continue;
            }

            $rows = $this->moduleRows($moduleKey);

            if ($rows === []) {
                continue;
            }

            $modules[] = [
                'key' => $moduleKey,
                'emoji' => $definition['emoji'],
                'title' => $definition['title'],
                'rows' => $rows,
            ];
        }

        return $modules;
    }

    /**
     * @return array{emoji: string, title: string}|null
     */
    private function moduleDefinition(string $moduleKey): ?array
    {
        if (!in_array($moduleKey, ModuleCatalog::moduleKeys(), true)) {
            return null;
        }

        return [
            'emoji' => ModuleCatalog::emojiFor($moduleKey),
            'title' => ModuleCatalog::titleFor($moduleKey),
        ];
    }

    /**
     * @return array<int, array{label: string, value: string|array<int, string>}>
     */
    private function moduleRows(string $moduleKey): array
    {
        $data = $this->moduleData($moduleKey);

        if ($data === null) {
            return [];
        }

        $rows = [];

        foreach ($data as $label => $value) {
            $formatted = $this->formatValue($value);

            if ($formatted === null) {
                continue;
            }

            $rows[] = [
                'label' => $label,
                'value' => $formatted,
            ];
        }

        if ($moduleKey === 'reading') {
            $books = $this->entry->books;

            if ($books->isNotEmpty()) {
                $rows[] = [
                    'label' => __('Books'),
                    'value' => $books->map(function ($book): string {
                        $status = $book->pivot?->status;
                        $suffix = $status ? sprintf(' (%s)', Str::headline($status)) : '';

                        return $book->name . $suffix;
                    })->all(),
                ];
            }
        }

        return $rows;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function moduleData(string $moduleKey): ?array
    {
        return match ($moduleKey) {
            'sleep' => [
                'Bedtime' => $this->entry->moduleSleep?->bedtime,
                'Wake up time' => $this->entry->moduleSleep?->wake_up_time,
                'Sleep duration (minutes)' => $this->entry->moduleSleep?->sleep_duration_in_minutes,
            ],
            'work' => [
                'Worked' => $this->entry->moduleWork?->worked,
                'Work mode' => $this->entry->moduleWork?->work_mode,
                'Work load' => $this->entry->moduleWork?->work_load,
                'Work procrastinated' => $this->entry->moduleWork?->work_procrastinated,
            ],
            'travel' => [
                'Has traveled today' => $this->entry->moduleTravel?->has_traveled_today,
                'Travel modes' => $this->entry->moduleTravel?->travel_mode,
                'Travel details' => $this->entry->moduleTravel?->travel_details,
            ],
            'weather' => [
                'Condition' => $this->entry->moduleWeather?->condition,
                'Temperature range' => $this->entry->moduleWeather?->temperature_range,
                'Precipitation' => $this->entry->moduleWeather?->precipitation,
                'Daylight' => $this->entry->moduleWeather?->daylight,
            ],
            'weather_influence' => [
                'Mood effect' => $this->entry->moduleWeatherInfluence?->mood_effect,
                'Energy effect' => $this->entry->moduleWeatherInfluence?->energy_effect,
                'Plans influence' => $this->entry->moduleWeatherInfluence?->plans_influence,
                'Outside time' => $this->entry->moduleWeatherInfluence?->outside_time,
            ],
            'shopping' => [
                'Has shopped today' => $this->entry->moduleShopping?->has_shopped_today,
                'Shopping type' => $this->entry->moduleShopping?->shopping_type,
                'Shopping intent' => $this->entry->moduleShopping?->shopping_intent,
                'Shopping context' => $this->entry->moduleShopping?->shopping_context,
                'Shopping for' => $this->entry->moduleShopping?->shopping_for,
            ],
            'meals' => [
                'Meal presence' => $this->entry->moduleMeals?->meal_presence,
                'Meal type' => $this->entry->moduleMeals?->meal_type,
                'Social context' => $this->entry->moduleMeals?->social_context,
                'Has notes' => $this->entry->moduleMeals?->has_notes,
                'Notes' => $this->entry->moduleMeals?->notes,
            ],
            'kids' => [
                'Had kids today' => $this->entry->moduleKids?->had_kids_today,
            ],
            'day_type' => [
                'Day type' => $this->entry->moduleDayType?->day_type,
            ],
            'primary_obligation' => [
                'Primary obligation' => $this->entry->modulePrimaryObligation?->primary_obligation,
            ],
            'physical_activity' => [
                'Has done physical activity' => $this->entry->modulePhysicalActivity?->has_done_physical_activity,
                'Activity type' => $this->entry->modulePhysicalActivity?->activity_type,
                'Activity intensity' => $this->entry->modulePhysicalActivity?->activity_intensity,
            ],
            'health' => [
                'Health' => $this->entry->moduleHealth?->health,
            ],
            'hygiene' => [
                'Showered' => $this->entry->moduleHygiene?->showered,
                'Brushed teeth' => $this->entry->moduleHygiene?->brushed_teeth,
                'Skincare' => $this->entry->moduleHygiene?->skincare,
            ],
            'mood' => [
                'Mood' => $this->entry->moduleMood?->mood,
            ],
            'reading' => [
                'Did read today' => $this->entry->moduleReading?->did_read_today,
                'Reading amount' => $this->entry->moduleReading?->reading_amount,
                'Mental state' => $this->entry->moduleReading?->mental_state,
                'Reading feel' => $this->entry->moduleReading?->reading_feel,
                'Want continue' => $this->entry->moduleReading?->want_continue,
                'Reading limit' => $this->entry->moduleReading?->reading_limit,
            ],
            'sexual_activity' => [
                'Had sexual activity' => $this->entry->moduleSexualActivity?->had_sexual_activity,
                'Sexual activity type' => $this->entry->moduleSexualActivity?->sexual_activity_type,
            ],
            'energy' => [
                'Energy' => $this->entry->moduleEnergy?->energy,
            ],
            'cognitive_load' => [
                'Cognitive load' => $this->entry->moduleCognitiveLoad?->cognitive_load,
                'Primary source' => $this->entry->moduleCognitiveLoad?->primary_source,
                'Load quality' => $this->entry->moduleCognitiveLoad?->load_quality,
            ],
            'social_density' => [
                'Social density' => $this->entry->moduleSocialDensity?->social_density,
            ],
            default => null,
        };
    }

    private function formatValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            $formatted = array_filter(array_map($this->formatScalar(...), $value));

            return $formatted !== [] ? implode(', ', $formatted) : null;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $value = TextSanitizer::plainText($value);

        return $this->formatScalar($value);
    }

    private function formatScalar(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = is_string($value) ? mb_trim($value) : (string) $value;

        if ($value === '') {
            return null;
        }

        if (in_array($value, ['yes', 'no'], true)) {
            return $value === 'yes' ? 'Yes' : 'No';
        }

        if (str_contains($value, '_') || str_contains($value, '-')) {
            return Str::headline($value);
        }

        return $value;
    }

    private function notesMarkdown(): ?string
    {
        $notes = new NotesPresenter($this->entry)->build();

        return $notes['notes_markdown'];
    }

    private function resolveLayout(): ?Layout
    {
        if ($this->entry->layout_id) {
            return $this->entry->layout;
        }

        return $this->entry
            ->journal
            ->layouts()
            ->where('is_active', true)
            ->first();
    }
}
