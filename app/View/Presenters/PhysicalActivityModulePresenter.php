<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModulePhysicalActivity;

final readonly class PhysicalActivityModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $modulePhysicalActivity = $this->entry->modulePhysicalActivity;

        return [
            'physical_activity_url' => route('journal.entry.physical-activity.update', [
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
            'has_done_physical_activity' => $modulePhysicalActivity?->has_done_physical_activity,
            'activity_types' => $this->activityTypes($modulePhysicalActivity?->activity_type),
            'activity_intensities' => $this->activityIntensities($modulePhysicalActivity?->activity_intensity),
            'display_reset' => $modulePhysicalActivity?->has_done_physical_activity !== null
                || $modulePhysicalActivity?->activity_type !== null
                || $modulePhysicalActivity?->activity_intensity !== null,
        ];
    }

    private function activityTypes(?string $selectedType): array
    {
        return collect(ModulePhysicalActivity::ACTIVITY_TYPES)->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'running' => __('Running'),
                'cycling' => __('Cycling'),
                'swimming' => __('Swimming'),
                'gym' => __('Gym'),
                'walking' => __('Walking'),
                default => $value,
            },
            'is_selected' => $selectedType === $value,
        ])->all();
    }

    private function activityIntensities(?string $selectedIntensity): array
    {
        return collect(ModulePhysicalActivity::ACTIVITY_INTENSITIES)->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'light' => __('Light'),
                'moderate' => __('Moderate'),
                'intense' => __('Intense'),
                default => $value,
            },
            'is_selected' => $selectedIntensity === $value,
        ])->all();
    }
}
