<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleSocialDensity;

final readonly class SocialDensityModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $moduleSocialDensity = $this->entry->moduleSocialDensity;

        return [
            'social_density_url' => route('journal.entry.social-density.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.social-density.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'social_density_options' => $this->socialDensityOptions(),
            'display_reset' => $moduleSocialDensity?->social_density !== null,
        ];
    }

    private function socialDensityOptions(): array
    {
        $moduleSocialDensity = $this->entry->moduleSocialDensity;

        return collect(ModuleSocialDensity::SOCIAL_DENSITY_VALUES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'alone' => __('Alone'),
                'few people' => __('Few people'),
                'crowd' => __('Crowd'),
                'too much' => __('Too much'),
                default => $value,
            },
            'is_selected' => $moduleSocialDensity?->social_density === $value,
        ])->all();
    }
}
