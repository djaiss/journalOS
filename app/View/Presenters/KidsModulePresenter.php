<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class KidsModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $moduleKids = $this->entry->moduleKids;

        return [
            'had_kids_today_url' => route('journal.entry.kids.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.kids.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'display_reset' => $moduleKids?->had_kids_today !== null,
        ];
    }
}
