<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleWeatherInfluence;

final readonly class WeatherInfluenceModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $module = $this->entry->moduleWeatherInfluence;

        return [
            'weather_influence_url' => route('journal.entry.weather-influence.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.weather-influence.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'mood_effect_options' => $this->moodEffectOptions(),
            'energy_effect_options' => $this->energyEffectOptions(),
            'plans_influence_options' => $this->plansInfluenceOptions(),
            'outside_time_options' => $this->outsideTimeOptions(),
            'display_reset' => $module !== null
                && ($module->mood_effect !== null
                    || $module->energy_effect !== null
                    || $module->plans_influence !== null
                    || $module->outside_time !== null),
        ];
    }

    private function moodEffectOptions(): array
    {
        $moodEffect = $this->entry->moduleWeatherInfluence?->mood_effect;

        return collect(ModuleWeatherInfluence::MOOD_EFFECTS)->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'positive' => __('Positive'),
                'slight' => __('Slight'),
                'none' => __('None'),
                'negative' => __('Negative'),
                default => $value,
            },
            'is_selected' => $moodEffect === $value,
        ])->all();
    }

    private function energyEffectOptions(): array
    {
        $energyEffect = $this->entry->moduleWeatherInfluence?->energy_effect;

        return collect(ModuleWeatherInfluence::ENERGY_EFFECTS)->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'boosted' => __('Boosted'),
                'neutral' => __('Neutral'),
                'drained' => __('Drained'),
                default => $value,
            },
            'is_selected' => $energyEffect === $value,
        ])->all();
    }

    private function plansInfluenceOptions(): array
    {
        $plansInfluence = $this->entry->moduleWeatherInfluence?->plans_influence;

        return collect(ModuleWeatherInfluence::PLANS_INFLUENCES)->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'none' => __('None'),
                'slight' => __('Slight'),
                'significant' => __('Significant'),
                default => $value,
            },
            'is_selected' => $plansInfluence === $value,
        ])->all();
    }

    private function outsideTimeOptions(): array
    {
        $outsideTime = $this->entry->moduleWeatherInfluence?->outside_time;

        return collect(ModuleWeatherInfluence::OUTSIDE_TIMES)->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'a_lot' => __('A lot'),
                'some' => __('Some'),
                'barely' => __('Barely'),
                'not_at_all' => __('Not at all'),
                default => $value,
            },
            'is_selected' => $outsideTime === $value,
        ])->all();
    }
}
