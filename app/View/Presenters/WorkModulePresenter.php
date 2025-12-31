<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class WorkModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $hasWorkedURL = route('journal.entry.work.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $workModeURL = route('journal.entry.work.mode.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $resetUrl = route('journal.entry.work.reset', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        return [
            'has_worked_url' => $hasWorkedURL,
            'work_mode_url' => $workModeURL,
            'reset_url' => $resetUrl,
            'display_reset' => ! is_null($this->entry->worked) || ! is_null($this->entry->work_mode) || ! is_null($this->entry->work_load) || ! is_null($this->entry->work_procrastinated),
        ];
    }
}
