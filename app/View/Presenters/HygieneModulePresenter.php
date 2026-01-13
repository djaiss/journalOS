<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class HygieneModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $module = $this->entry->moduleHygiene;
        $displayReset = $module?->showered !== null
            || $module?->brushed_teeth !== null
            || $module?->skincare !== null;

        return [
            'hygiene_url' => route('journal.entry.hygiene.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.hygiene.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'showered' => $module?->showered,
            'showered_options' => $this->yesNoOptions($module?->showered),
            'brushed_teeth' => $module?->brushed_teeth,
            'brushed_teeth_options' => $this->brushedTeethOptions($module?->brushed_teeth),
            'skincare' => $module?->skincare,
            'skincare_options' => $this->yesNoOptions($module?->skincare),
            'display_reset' => $displayReset,
        ];
    }

    private function yesNoOptions(?string $value): array
    {
        return collect(['yes', 'no'])->map(fn($option) => [
            'value' => $option,
            'label' => $option === 'yes' ? __('Yes') : __('No'),
            'is_selected' => $value === $option,
        ])->all();
    }

    private function brushedTeethOptions(?string $value): array
    {
        return collect(['no', 'am', 'pm'])->map(fn($option) => [
            'value' => $option,
            'label' => match ($option) {
                'no' => __('No'),
                'am' => __('AM'),
                'pm' => __('PM'),
                default => $option,
            },
            'is_selected' => $value === $option,
        ])->all();
    }
}
