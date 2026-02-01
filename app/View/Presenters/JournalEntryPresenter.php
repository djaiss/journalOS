<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\Layout;
use App\Models\JournalEntry;

final readonly class JournalEntryPresenter
{
    public function __construct(private JournalEntry $entry) {}

    public function build(): array
    {
        $layout = $this->resolveLayout();
        $columns = $this->buildColumns($layout);
        $notes = new NotesPresenter($this->entry)->build();

        return [
            'columns' => $columns,
            'notes' => $notes,
            'layout_columns_count' => $layout->columns_count ?? 0,
        ];
    }

    /**
     * @return array<int, array<int, array{key: string, view: string, data: array<string, mixed>}>>
     */
    private function buildColumns(?Layout $layout): array
    {
        $columns = [];

        if (! $layout) {
            return $columns;
        }

        for ($column = 1; $column <= $layout->columns_count; $column++) {
            $columns[$column] = [];
        }

        $layoutModules = $layout->layoutModules()
            ->orderBy('column_number')
            ->orderBy('position')
            ->get();

        foreach ($layoutModules as $layoutModule) {
            if (! $this->isModuleVisible($layoutModule->module_key)) {
                continue;
            }

            $module = $this->buildModulePayload($layoutModule->module_key);

            if (! $module) {
                continue;
            }

            $columns[$layoutModule->column_number][] = $module;
        }

        return $columns;
    }

    /**
     * @return array{key: string, view: string, data: array<string, mixed>}|null
     */
    private function buildModulePayload(string $moduleKey): ?array
    {
        return match ($moduleKey) {
            'sleep' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.sleep',
                'data' => ['module' => new SleepModulePresenter($this->entry)->build('20:00', '06:00')],
            ],
            'work' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.work',
                'data' => ['module' => new WorkModulePresenter($this->entry)->build()],
            ],
            'travel' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.travel',
                'data' => ['module' => new TravelModulePresenter($this->entry)->build()],
            ],
            'weather' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.weather',
                'data' => ['module' => new WeatherModulePresenter($this->entry)->build()],
            ],
            'weather_influence' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.weather_influence',
                'data' => ['module' => new WeatherInfluenceModulePresenter($this->entry)->build()],
            ],
            'shopping' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.shopping',
                'data' => ['module' => new ShoppingModulePresenter($this->entry)->build()],
            ],
            'meals' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.meals',
                'data' => ['module' => new MealsModulePresenter($this->entry)->build()],
            ],
            'kids' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.kids',
                'data' => [
                    'entry' => $this->entry,
                    'module' => new KidsModulePresenter($this->entry)->build(),
                ],
            ],
            'day_type' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.day_type',
                'data' => ['module' => new DayTypeModulePresenter($this->entry)->build()],
            ],
            'primary_obligation' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.primary_obligation',
                'data' => ['module' => new PrimaryObligationModulePresenter($this->entry)->build()],
            ],
            'physical_activity' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.physical_activity',
                'data' => ['module' => new PhysicalActivityModulePresenter($this->entry)->build()],
            ],
            'health' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.health',
                'data' => ['module' => new HealthModulePresenter($this->entry)->build()],
            ],
            'hygiene' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.hygiene',
                'data' => ['module' => new HygieneModulePresenter($this->entry)->build()],
            ],
            'mood' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.mood',
                'data' => ['module' => new MoodModulePresenter($this->entry)->build()],
            ],
            'reading' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.reading',
                'data' => ['module' => new ReadingModulePresenter($this->entry)->build()],
            ],
            'sexual_activity' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.sexual_activity',
                'data' => [
                    'entry' => $this->entry,
                    'module' => new SexualActivityModulePresenter($this->entry)->build(),
                ],
            ],
            'energy' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.energy',
                'data' => ['module' => new EnergyModulePresenter($this->entry)->build()],
            ],
            'cognitive_load' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.cognitive_load',
                'data' => ['module' => new CognitiveLoadModulePresenter($this->entry)->build()],
            ],
            'social_density' => [
                'key' => $moduleKey,
                'view' => 'app.journal.entry.partials.social_density',
                'data' => ['module' => new SocialDensityModulePresenter($this->entry)->build()],
            ],
            default => null,
        };
    }

    private function isModuleVisible(string $moduleKey): bool
    {
        return true;
    }

    private function resolveLayout(): ?Layout
    {
        if ($this->entry->layout_id) {
            return $this->entry->layout;
        }

        return $this->entry->journal->layouts()
            ->where('is_active', true)
            ->first();
    }
}
