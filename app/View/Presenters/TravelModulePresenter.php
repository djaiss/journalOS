<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class TravelModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $hasTraveledURL = route('journal.entry.travel.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $travelModeURL = route('journal.entry.travel.mode.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $travelModes = collect(['car', 'plane', 'train', 'bike', 'bus', 'walk', 'boat', 'other'])->map(fn($mode) => [
            'value' => $mode,
            'label' => match ($mode) {
                'car' => __('Car'),
                'plane' => __('Plane'),
                'train' => __('Train'),
                'bike' => __('Bike'),
                'bus' => __('Bus'),
                'walk' => __('Walk'),
                'boat' => __('Boat'),
                'other' => __('Other'),
                default => $mode,
            },
            'is_selected' => is_array($this->entry->travel_mode) && in_array($mode, $this->entry->travel_mode, true),
        ]);

        return [
            'has_traveled_url' => $hasTraveledURL,
            'travel_mode_url' => $travelModeURL,
            'travel_modes' => $travelModes,
        ];
    }
}
