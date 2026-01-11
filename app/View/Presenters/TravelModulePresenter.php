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
        $moduleTravel = $this->entry->moduleTravel;
        $travelUrl = route('journal.entry.travel.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $resetUrl = route('journal.entry.travel.reset', [
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
            'is_selected' => is_array($moduleTravel?->travel_mode) && in_array($mode, $moduleTravel->travel_mode, true),
        ]);

        return [
            'has_traveled_today' => $moduleTravel?->has_traveled_today,
            'travel_url' => $travelUrl,
            'travel_mode' => $moduleTravel?->travel_mode,
            'travel_modes' => $travelModes,
            'reset_url' => $resetUrl,
            'display_reset' => ! is_null($moduleTravel?->has_traveled_today) || ! is_null($moduleTravel?->travel_mode),
        ];
    }
}
