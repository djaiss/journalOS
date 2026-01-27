<?php

declare(strict_types=1);

namespace App\Actions;

use App\Helpers\ModuleCatalog;
use App\Helpers\TextSanitizer;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final readonly class GetJournalEntryMarkdownForLLM
{
    public function __construct(
        private Journal $journal,
        private int $year,
        private int $month,
        private int $day,
    ) {}

    public function execute(): string
    {
        $this->validateDate();

        $entry = $this->retrieveEntry();
        $layout = $this->resolveLayout($entry);
        $moduleKeys = $this->resolveModuleKeys($layout);

        $entry->loadMissing(array_merge(
            self::relationshipsForModules($moduleKeys),
            ['richTextNotes'],
        ));

        return $this->buildMarkdown($entry, $moduleKeys);
    }

    private function validateDate(): void
    {
        if (! checkdate($this->month, $this->day, $this->year)) {
            throw ValidationException::withMessages([
                'date' => 'Date is invalid.',
            ]);
        }
    }

    private function retrieveEntry(): JournalEntry
    {
        return JournalEntry::query()
            ->where('journal_id', $this->journal->id)
            ->where('day', $this->day)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->firstOrFail();
    }

    private function resolveLayout(JournalEntry $entry): ?Layout
    {
        if ($entry->layout_id) {
            return $entry->layout;
        }

        return $this->journal->layouts()
            ->where('is_active', true)
            ->first();
    }

    /**
     * @return array<int, string>
     */
    private function resolveModuleKeys(?Layout $layout): array
    {
        return self::moduleKeysForLayout($layout);
    }

    /**
     * @param  array<int, string>  $moduleKeys
     * @return array<int, string>
     */
    /**
     * @param  array<int, string>  $moduleKeys
     * @return array<int, string>
     */
    public static function relationshipsForModules(array $moduleKeys): array
    {
        $relationshipMap = self::relationshipMap();

        $relationships = [];

        foreach ($moduleKeys as $moduleKey) {
            if (array_key_exists($moduleKey, $relationshipMap)) {
                $relationships[] = $relationshipMap[$moduleKey];
            }
        }

        if (in_array('reading', $moduleKeys, true)) {
            $relationships[] = 'books';
        }

        return array_values(array_unique($relationships));
    }

    /**
     * @return array<int, string>
     */
    public static function allRelationships(): array
    {
        $relationships = array_values(self::relationshipMap());
        $relationships[] = 'books';

        return array_values(array_unique($relationships));
    }

    /**
     * @return array<int, string>
     */
    public static function moduleKeysForLayout(?Layout $layout): array
    {
        if (! $layout) {
            return [];
        }

        $modules = $layout->relationLoaded('layoutModules')
            ? $layout->layoutModules->sortBy([
                ['column_number', 'asc'],
                ['position', 'asc'],
            ])
            : $layout->layoutModules()
                ->orderBy('column_number')
                ->orderBy('position')
                ->get();

        return $modules->pluck('module_key')->all();
    }

    /**
     * @param  array<int, string>  $moduleKeys
     */
    private function buildMarkdown(JournalEntry $entry, array $moduleKeys): string
    {
        return self::entryMarkdown($this->journal, $entry, $moduleKeys);
    }

    /**
     * @param  array<int, string>  $moduleKeys
     */
    public static function entryMarkdown(Journal $journal, JournalEntry $entry, array $moduleKeys): string
    {
        $lines = [];
        $lines[] = sprintf('# Journal entry — %04d-%02d-%02d', $entry->year, $entry->month, $entry->day);
        $lines[] = sprintf('Journal: %s', $journal->name);
        $lines[] = '';

        $content = self::entryContent($entry, $moduleKeys);
        if ($content !== '') {
            $lines[] = $content;
        }

        return mb_rtrim(implode("\n", $lines)) . "\n";
    }

    /**
     * @param  array<int, string>  $moduleKeys
     */
    public static function entryContent(JournalEntry $entry, array $moduleKeys): string
    {
        $lines = [];

        $notes = self::notesFor($entry);
        if ($notes !== null) {
            $lines[] = '## Notes';
            $lines[] = $notes;
            $lines[] = '';
        }

        if ($moduleKeys !== []) {
            $lines[] = '## Modules';

            foreach ($moduleKeys as $moduleKey) {
                $moduleLines = self::moduleLines($entry, $moduleKey);

                if ($moduleLines === []) {
                    continue;
                }

                $lines[] = '### ' . ModuleCatalog::labelFor($moduleKey);
                array_push($lines, ...$moduleLines);
                $lines[] = '';
            }
        }

        return mb_rtrim(implode("\n", $lines));
    }

    private static function notesFor(JournalEntry $entry): ?string
    {
        $richTextNotes = $entry->richTextNotes;
        $notes = $richTextNotes?->body?->toPlainText();
        $notes = $notes ? TextSanitizer::plainText($notes) : '';

        return $notes !== '' ? $notes : null;
    }

    /**
     * @return array<int, string>
     */
    private static function moduleLines(JournalEntry $entry, string $moduleKey): array
    {
        $data = self::moduleData($entry, $moduleKey);
        if ($data === null) {
            return [];
        }

        $lines = [];

        foreach ($data as $label => $value) {
            $formatted = self::formatValue($value);

            if ($formatted === null) {
                continue;
            }

            $lines[] = sprintf('- %s: %s', $label, $formatted);
        }

        if ($moduleKey === 'reading') {
            $books = $entry->books;

            if ($books->isNotEmpty()) {
                $lines[] = '- Books:';

                foreach ($books as $book) {
                    $status = $book->pivot?->status;
                    $suffix = $status ? sprintf(' (%s)', $status) : '';
                    $lines[] = sprintf('  - %s%s', $book->name, $suffix);
                }
            }
        }

        return $lines !== [] ? $lines : ['- No data'];
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function moduleData(JournalEntry $entry, string $moduleKey): ?array
    {
        return match ($moduleKey) {
            'sleep' => [
                'Bedtime' => $entry->moduleSleep?->bedtime,
                'Wake up time' => $entry->moduleSleep?->wake_up_time,
                'Sleep duration (minutes)' => $entry->moduleSleep?->sleep_duration_in_minutes,
            ],
            'work' => [
                'Worked' => $entry->moduleWork?->worked,
                'Work mode' => $entry->moduleWork?->work_mode,
                'Work load' => $entry->moduleWork?->work_load,
                'Work procrastinated' => $entry->moduleWork?->work_procrastinated,
            ],
            'travel' => [
                'Has traveled today' => $entry->moduleTravel?->has_traveled_today,
                'Travel modes' => $entry->moduleTravel?->travel_mode,
                'Travel details' => $entry->moduleTravel?->travel_details,
            ],
            'weather' => [
                'Condition' => $entry->moduleWeather?->condition,
                'Temperature range' => $entry->moduleWeather?->temperature_range,
                'Precipitation' => $entry->moduleWeather?->precipitation,
                'Daylight' => $entry->moduleWeather?->daylight,
            ],
            'weather_influence' => [
                'Mood effect' => $entry->moduleWeatherInfluence?->mood_effect,
                'Energy effect' => $entry->moduleWeatherInfluence?->energy_effect,
                'Plans influence' => $entry->moduleWeatherInfluence?->plans_influence,
                'Outside time' => $entry->moduleWeatherInfluence?->outside_time,
            ],
            'shopping' => [
                'Has shopped today' => $entry->moduleShopping?->has_shopped_today,
                'Shopping type' => $entry->moduleShopping?->shopping_type,
                'Shopping intent' => $entry->moduleShopping?->shopping_intent,
                'Shopping context' => $entry->moduleShopping?->shopping_context,
                'Shopping for' => $entry->moduleShopping?->shopping_for,
            ],
            'meals' => [
                'Meal presence' => $entry->moduleMeals?->meal_presence,
                'Meal type' => $entry->moduleMeals?->meal_type,
                'Social context' => $entry->moduleMeals?->social_context,
                'Has notes' => $entry->moduleMeals?->has_notes,
                'Notes' => $entry->moduleMeals?->notes,
            ],
            'kids' => [
                'Had kids today' => $entry->moduleKids?->had_kids_today,
            ],
            'day_type' => [
                'Day type' => $entry->moduleDayType?->day_type,
            ],
            'primary_obligation' => [
                'Primary obligation' => $entry->modulePrimaryObligation?->primary_obligation,
            ],
            'physical_activity' => [
                'Has done physical activity' => $entry->modulePhysicalActivity?->has_done_physical_activity,
                'Activity type' => $entry->modulePhysicalActivity?->activity_type,
                'Activity intensity' => $entry->modulePhysicalActivity?->activity_intensity,
            ],
            'health' => [
                'Health' => $entry->moduleHealth?->health,
            ],
            'hygiene' => [
                'Showered' => $entry->moduleHygiene?->showered,
                'Brushed teeth' => $entry->moduleHygiene?->brushed_teeth,
                'Skincare' => $entry->moduleHygiene?->skincare,
            ],
            'mood' => [
                'Mood' => $entry->moduleMood?->mood,
            ],
            'reading' => [
                'Did read today' => $entry->moduleReading?->did_read_today,
                'Reading amount' => $entry->moduleReading?->reading_amount,
                'Mental state' => $entry->moduleReading?->mental_state,
                'Reading feel' => $entry->moduleReading?->reading_feel,
                'Want continue' => $entry->moduleReading?->want_continue,
                'Reading limit' => $entry->moduleReading?->reading_limit,
            ],
            'sexual_activity' => [
                'Had sexual activity' => $entry->moduleSexualActivity?->had_sexual_activity,
                'Sexual activity type' => $entry->moduleSexualActivity?->sexual_activity_type,
            ],
            'energy' => [
                'Energy' => $entry->moduleEnergy?->energy,
            ],
            'cognitive_load' => [
                'Cognitive load' => $entry->moduleCognitiveLoad?->cognitive_load,
                'Primary source' => $entry->moduleCognitiveLoad?->primary_source,
                'Load quality' => $entry->moduleCognitiveLoad?->load_quality,
            ],
            'social_density' => [
                'Social density' => $entry->moduleSocialDensity?->social_density,
            ],
            default => null,
        };
    }

    private static function formatValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            $formatted = array_filter(array_map(self::formatScalar(...), $value));

            return $formatted !== [] ? implode(', ', $formatted) : null;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (! is_string($value)) {
            return null;
        }

        $value = TextSanitizer::plainText($value);

        return self::formatScalar($value);
    }

    private static function formatScalar(mixed $value): ?string
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

    /**
     * @return array<string, string>
     */
    private static function relationshipMap(): array
    {
        return [
            'sleep' => 'moduleSleep',
            'work' => 'moduleWork',
            'travel' => 'moduleTravel',
            'weather' => 'moduleWeather',
            'weather_influence' => 'moduleWeatherInfluence',
            'shopping' => 'moduleShopping',
            'meals' => 'moduleMeals',
            'kids' => 'moduleKids',
            'day_type' => 'moduleDayType',
            'primary_obligation' => 'modulePrimaryObligation',
            'physical_activity' => 'modulePhysicalActivity',
            'health' => 'moduleHealth',
            'hygiene' => 'moduleHygiene',
            'mood' => 'moduleMood',
            'reading' => 'moduleReading',
            'sexual_activity' => 'moduleSexualActivity',
            'energy' => 'moduleEnergy',
            'cognitive_load' => 'moduleCognitiveLoad',
            'social_density' => 'moduleSocialDensity',
        ];
    }
}
