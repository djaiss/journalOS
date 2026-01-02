<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class MoodModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        return [
            'mood_url' => route('journal.entry.mood.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.mood.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'mood_options' => $this->moodOptions(),
            'display_reset' => $this->entry->mood !== null,
        ];
    }

    private function moodOptions(): array
    {
        return collect(['terrible', 'bad', 'okay', 'good', 'great'])->map(fn($value) => [
            'value' => $value,
            'label' => match ($value) {
                'terrible' => __('Terrible'),
                'bad' => __('Bad'),
                'okay' => __('Okay'),
                'good' => __('Good'),
                'great' => __('Great'),
                default => $value,
            },
            'is_selected' => $this->entry->mood === $value,
        ])->all();
    }
}
