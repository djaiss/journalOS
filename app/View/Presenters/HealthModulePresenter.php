<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleHealth;

final readonly class HealthModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $health = $this->entry->moduleHealth?->health;

        return [
            'health_url' => route('journal.entry.health.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.health.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'health_options' => $this->healthOptions(),
            'display_reset' => $health !== null,
        ];
    }

    private function healthOptions(): array
    {
        $health = $this->entry->moduleHealth?->health;

        return collect(ModuleHealth::HEALTH_VALUES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'not great' => __('Not great'),
                'okay' => __('Okay'),
                'good' => __('Good'),
                default => $value,
            },
            'is_selected' => $health === $value,
        ])->all();
    }
}
