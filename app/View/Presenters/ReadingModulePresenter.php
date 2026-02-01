<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\Book;
use App\Models\JournalEntry;
use App\Models\ModuleReading;

final readonly class ReadingModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $didReadToday = $this->entry->moduleReading?->did_read_today;

        return [
            'reading_url' => route('journal.entry.reading.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.reading.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'books_url' => route('journal.entry.reading.books.store', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
            'did_read_today' => $didReadToday,
            'books' => $this->books(),
            'book_suggestions' => $this->bookSuggestions(),
            'reading_amounts' => $this->readingAmounts(),
            'mental_states' => $this->mentalStates(),
            'reading_feels' => $this->readingFeels(),
            'want_continue_options' => $this->wantContinueOptions(),
            'reading_limits' => $this->readingLimits(),
            'display_reset' => $this->hasReadingData(),
        ];
    }

    private function books(): array
    {
        $this->entry->loadMissing('books');

        return $this->entry
            ->books
            ->sortBy(fn (Book $book) => $book->name)
            ->map(fn (Book $book) => [
                'id' => $book->id,
                'name' => $book->name,
                'status' => $book->pivot?->status,
            ])
            ->values()
            ->all();
    }

    private function bookSuggestions(): array
    {
        $books = Book::query()
            ->where('user_id', $this->entry->journal->user_id)
            ->get(['id', 'name']);

        return $books
            ->map(fn (Book $book) => $book->name)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function readingAmounts(): array
    {
        $readingAmount = $this->entry->moduleReading?->reading_amount;

        return collect(ModuleReading::READING_AMOUNTS)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'a few pages' => __('A few pages'),
                'one solid session' => __('One solid session'),
                'multiple sessions' => __('Multiple sessions'),
                'deep immersion' => __('Deep immersion'),
                default => $value,
            },
            'is_selected' => $readingAmount === $value,
        ])->all();
    }

    private function mentalStates(): array
    {
        $mentalState = $this->entry->moduleReading?->mental_state;

        return collect(ModuleReading::MENTAL_STATES)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'stimulated' => __('Stimulated'),
                'calm' => __('Calm'),
                'neutral' => __('Neutral'),
                'overloaded' => __('Overloaded'),
                default => $value,
            },
            'is_selected' => $mentalState === $value,
        ])->all();
    }

    private function readingFeels(): array
    {
        $readingFeel = $this->entry->moduleReading?->reading_feel;

        return collect(ModuleReading::READING_FEELS)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'effortless' => __('Effortless'),
                'engaging' => __('Engaging'),
                'demanding' => __('Demanding'),
                'hard to focus' => __('Hard to focus'),
                default => $value,
            },
            'is_selected' => $readingFeel === $value,
        ])->all();
    }

    private function wantContinueOptions(): array
    {
        $wantContinue = $this->entry->moduleReading?->want_continue;

        return collect(ModuleReading::WANT_CONTINUE_OPTIONS)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'strongly' => __('Strongly'),
                'somewhat' => __('Somewhat'),
                'not really' => __('Not really'),
                default => $value,
            },
            'is_selected' => $wantContinue === $value,
        ])->all();
    }

    private function readingLimits(): array
    {
        $readingLimit = $this->entry->moduleReading?->reading_limit;

        return collect(ModuleReading::READING_LIMITS)->map(fn ($value) => [
            'value' => $value,
            'label' => match ($value) {
                'time' => __('Time'),
                'energy' => __('Energy'),
                'distraction' => __('Distraction'),
                'nothing' => __('Nothing'),
                default => $value,
            },
            'is_selected' => $readingLimit === $value,
        ])->all();
    }

    private function hasReadingData(): bool
    {
        $moduleReading = $this->entry->moduleReading;

        if (
            $moduleReading !== null
            && (
                $moduleReading->did_read_today !== null
                || $moduleReading->reading_amount !== null
                || $moduleReading->mental_state !== null
                || $moduleReading->reading_feel !== null
                || $moduleReading->want_continue !== null
                || $moduleReading->reading_limit !== null
            )
        ) {
            return true;
        }

        return $this->entry->books()->exists();
    }
}
