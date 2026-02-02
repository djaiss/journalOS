<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleSocialEvents;

final readonly class SocialEventsModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $moduleSocialEvents = $this->entry->moduleSocialEvents;

        return [
            'social_events_url' => route('journal.entry.social-events.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.social-events.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'event_type_options' => $this->eventTypeOptions(),
            'tone_options' => $this->toneOptions(),
            'duration_options' => $this->durationOptions(),
            'event_type' => $moduleSocialEvents?->event_type,
            'tone' => $moduleSocialEvents?->tone,
            'duration' => $moduleSocialEvents?->duration,
            'has_event_type' => $moduleSocialEvents?->event_type !== null,
            'has_tone' => $moduleSocialEvents?->tone !== null,
            'display_reset' => $moduleSocialEvents?->event_type !== null
                || $moduleSocialEvents?->tone !== null
                || $moduleSocialEvents?->duration !== null,
        ];
    }

    private function eventTypeOptions(): array
    {
        $moduleSocialEvents = $this->entry->moduleSocialEvents;

        return collect(ModuleSocialEvents::EVENT_TYPE_VALUES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'friends' => __('Friends'),
                'family' => __('Family'),
                'work' => __('Work'),
                'networking' => __('Networking'),
                'romantic' => __('Romantic'),
                'other' => __('Other'),
                default => $value,
            },
            'is_selected' => $moduleSocialEvents?->event_type === $value,
        ])->all();
    }

    private function toneOptions(): array
    {
        $moduleSocialEvents = $this->entry->moduleSocialEvents;

        return collect(ModuleSocialEvents::TONE_VALUES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'positive' => __('Positive'),
                'neutral' => __('Neutral'),
                'draining' => __('Draining'),
                default => $value,
            },
            'is_selected' => $moduleSocialEvents?->tone === $value,
        ])->all();
    }

    private function durationOptions(): array
    {
        $moduleSocialEvents = $this->entry->moduleSocialEvents;

        return collect(ModuleSocialEvents::DURATION_VALUES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'short' => __('Short'),
                'medium' => __('Medium'),
                'long' => __('Long'),
                default => $value,
            },
            'is_selected' => $moduleSocialEvents?->duration === $value,
        ])->all();
    }
}
