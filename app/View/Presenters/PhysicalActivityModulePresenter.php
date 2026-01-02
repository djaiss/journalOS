<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class PhysicalActivityModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        return [
            'has_done_url' => route('journal.entry.physical-activity.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'activity_type_url' => route('journal.entry.physical-activity.type.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'activity_intensity_url' => route('journal.entry.physical-activity.intensity.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.physical-activity.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'activity_types' => $this->activityTypes(),
            'activity_intensities' => $this->activityIntensities(),
            'display_reset' => $this->entry->has_done_physical_activity !== null,
        ];
    }

    private function activityTypes(): array
    {
        return collect(['running', 'cycling', 'swimming', 'gym', 'walking'])->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'running' => __('Running'),
                'cycling' => __('Cycling'),
                'swimming' => __('Swimming'),
                'gym' => __('Gym'),
                'walking' => __('Walking'),
                default => $value,
            },
            'is_selected' => $this->entry->activity_type === $value,
        ])->all();
    }

    private function activityIntensities(): array
    {
        return collect(['light', 'moderate', 'intense'])->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'light' => __('Light'),
                'moderate' => __('Moderate'),
                'intense' => __('Intense'),
                default => $value,
            },
            'is_selected' => $this->entry->activity_intensity === $value,
        ])->all();
    }
}
