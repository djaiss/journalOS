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

        $workLoadURL = route('journal.entry.work.load.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $workProcrastinatedURL = route('journal.entry.work.procrastinated.update', [
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

        $workModes = collect(['remote', 'on-site', 'hybrid'])->map(fn($mode) => [
            'value' => $mode,
            'label' => match ($mode) {
                'remote' => __('Remote'),
                'on-site' => __('On-site'),
                'hybrid' => __('Hybrid'),
                default => $mode,
            },
            'is_selected' => $mode === $this->entry->work_mode,
        ]);

        $workLoads = collect(['light', 'medium', 'heavy'])->map(fn($load) => [
            'value' => $load,
            'label' => match ($load) {
                'light' => __('Light'),
                'medium' => __('Medium'),
                'heavy' => __('Heavy'),
                default => $load,
            },
            'is_selected' => $load === $this->entry->work_load,
        ]);

        return [
            'has_worked_url' => $hasWorkedURL,
            'work_mode_url' => $workModeURL,
            'work_modes' => $workModes,
            'work_load_url' => $workLoadURL,
            'work_loads' => $workLoads,
            'work_procrastinated_url' => $workProcrastinatedURL,
            'reset_url' => $resetUrl,
            'display_reset' => ! is_null($this->entry->worked) || ! is_null($this->entry->work_mode) || ! is_null($this->entry->work_load) || ! is_null($this->entry->work_procrastinated),
        ];
    }
}
