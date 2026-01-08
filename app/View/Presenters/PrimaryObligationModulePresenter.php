<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class PrimaryObligationModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $modulePrimaryObligation = $this->entry->modulePrimaryObligation;

        return [
            'primary_obligation_url' => route('journal.entry.primary-obligation.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.primary-obligation.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'primary_obligation_options' => $this->primaryObligationOptions(),
            'display_reset' => $modulePrimaryObligation?->primary_obligation !== null,
        ];
    }

    private function primaryObligationOptions(): array
    {
        $modulePrimaryObligation = $this->entry->modulePrimaryObligation;

        return collect(['work', 'family', 'personal', 'health', 'travel', 'none'])->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'work' => __('Work'),
                'family' => __('Family'),
                'personal' => __('Personal'),
                'health' => __('Health'),
                'travel' => __('Travel'),
                'none' => __('None'),
                default => $value,
            },
            'is_selected' => $modulePrimaryObligation?->primary_obligation === $value,
        ])->all();
    }
}
