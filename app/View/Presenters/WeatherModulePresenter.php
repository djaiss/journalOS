<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleWeather;

final readonly class WeatherModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $module = $this->entry->moduleWeather;

        return [
            'weather_url' => route('journal.entry.weather.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.weather.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'condition_options' => $this->conditionOptions(),
            'temperature_range_options' => $this->temperatureRangeOptions(),
            'precipitation_options' => $this->precipitationOptions(),
            'daylight_options' => $this->daylightOptions(),
            'display_reset' =>
                $module !== null
                    && (
                        $module->condition !== null
                        || $module->temperature_range !== null
                        || $module->precipitation !== null
                        || $module->daylight !== null
                    ),
        ];
    }

    private function conditionOptions(): array
    {
        $condition = $this->entry->moduleWeather?->condition;

        return collect(ModuleWeather::CONDITIONS)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'sunny' => __('Sunny'),
                'cloudy' => __('Cloudy'),
                'rain' => __('Rain'),
                'snow' => __('Snow'),
                'mixed' => __('Mixed'),
                default => $value,
            },
            'is_selected' => $condition === $value,
        ])->all();
    }

    private function temperatureRangeOptions(): array
    {
        $temperatureRange = $this->entry->moduleWeather?->temperature_range;

        return collect(ModuleWeather::TEMPERATURE_RANGES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'very_cold' => __('Very cold'),
                'cold' => __('Cold'),
                'mild' => __('Mild'),
                'warm' => __('Warm'),
                'hot' => __('Hot'),
                default => $value,
            },
            'is_selected' => $temperatureRange === $value,
        ])->all();
    }

    private function precipitationOptions(): array
    {
        $precipitation = $this->entry->moduleWeather?->precipitation;

        return collect(ModuleWeather::PRECIPITATION_LEVELS)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'none' => __('None'),
                'light' => __('Light'),
                'heavy' => __('Heavy'),
                default => $value,
            },
            'is_selected' => $precipitation === $value,
        ])->all();
    }

    private function daylightOptions(): array
    {
        $daylight = $this->entry->moduleWeather?->daylight;

        return collect(ModuleWeather::DAYLIGHT_VALUES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'very_short' => __('Very short'),
                'normal' => __('Normal'),
                'very_long' => __('Very long'),
                default => $value,
            },
            'is_selected' => $daylight === $value,
        ])->all();
    }
}
