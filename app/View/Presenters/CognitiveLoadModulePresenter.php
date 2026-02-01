<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleCognitiveLoad;

final readonly class CognitiveLoadModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $moduleCognitiveLoad = $this->entry->moduleCognitiveLoad;
        $cognitiveLoad = $moduleCognitiveLoad?->cognitive_load;

        return [
            'cognitive_load_url' => route('journal.entry.cognitive-load.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.cognitive-load.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'cognitive_load' => $cognitiveLoad,
            'cognitive_load_options' => $this->cognitiveLoadOptions(),
            'primary_source_options' => $this->primarySourceOptions(),
            'load_quality_options' => $this->loadQualityOptions(),
            'display_reset' =>
                $moduleCognitiveLoad?->cognitive_load !== null
                    || $moduleCognitiveLoad?->primary_source !== null
                    || $moduleCognitiveLoad?->load_quality !== null,
            'has_cognitive_load' => $cognitiveLoad !== null,
        ];
    }

    private function cognitiveLoadOptions(): array
    {
        $cognitiveLoad = $this->entry->moduleCognitiveLoad?->cognitive_load;

        return collect(ModuleCognitiveLoad::COGNITIVE_LOAD_LEVELS)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'very low' => __('Very low'),
                'low' => __('Low'),
                'high' => __('High'),
                'overwhelming' => __('Overwhelming'),
                default => $value,
            },
            'is_selected' => $cognitiveLoad === $value,
        ])->all();
    }

    private function primarySourceOptions(): array
    {
        $primarySource = $this->entry->moduleCognitiveLoad?->primary_source;

        return collect(ModuleCognitiveLoad::PRIMARY_SOURCES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'work' => __('Work'),
                'personal life' => __('Personal life'),
                'relationships' => __('Relationships'),
                'health' => __('Health'),
                'uncertainty' => __('Uncertainty'),
                'mixed' => __('Mixed'),
                default => $value,
            },
            'is_selected' => $primarySource === $value,
        ])->all();
    }

    private function loadQualityOptions(): array
    {
        $loadQuality = $this->entry->moduleCognitiveLoad?->load_quality;

        return collect(ModuleCognitiveLoad::LOAD_QUALITIES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'productive' => __('Productive'),
                'mixed' => __('Mixed'),
                'mostly wasteful' => __('Mostly wasteful'),
                default => $value,
            },
            'is_selected' => $loadQuality === $value,
        ])->all();
    }
}
