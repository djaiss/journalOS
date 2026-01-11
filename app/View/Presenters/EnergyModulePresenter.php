<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleEnergy;

final readonly class EnergyModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $energy = $this->entry->moduleEnergy?->energy;

        return [
            'energy_url' => route('journal.entry.energy.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.energy.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'energy_options' => $this->energyOptions(),
            'display_reset' => $energy !== null,
        ];
    }

    private function energyOptions(): array
    {
        $energy = $this->entry->moduleEnergy?->energy;

        return collect(ModuleEnergy::ENERGY_LEVELS)->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'very low' => __('Very low'),
                'low' => __('Low'),
                'normal' => __('Normal'),
                'high' => __('High'),
                'very high' => __('Very high'),
                default => $value,
            },
            'is_selected' => $energy === $value,
        ])->all();
    }
}
