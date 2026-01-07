<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class DayTypeModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $selectedDayType = $this->entry->moduleDayType?->day_type;

        $dayTypeURL = route('journal.entry.day-type.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $resetUrl = route('journal.entry.day-type.reset', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $dayTypes = collect(['workday', 'day off', 'weekend', 'vacation', 'sick day'])->map(fn($type) => [
            'value' => $type,
            'label' => match ($type) {
                'workday' => __('Workday'),
                'day off' => __('Day off'),
                'weekend' => __('Weekend'),
                'vacation' => __('Vacation'),
                'sick day' => __('Sick day'),
                default => $type,
            },
            'is_selected' => $type === $selectedDayType,
        ]);

        return [
            'day_type_url' => $dayTypeURL,
            'day_types' => $dayTypes,
            'reset_url' => $resetUrl,
            'display_reset' => ! is_null($selectedDayType),
        ];
    }
}
