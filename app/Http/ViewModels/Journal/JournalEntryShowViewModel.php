<?php

declare(strict_types=1);

namespace App\Http\ViewModels\Journal;

use App\Models\JournalEntry;

final readonly class JournalEntryShowViewModel
{
    public function __construct(
        private JournalEntry $journalEntry,
        private string $startBedTime = '20:00',
        private string $startWakeUpTime = '06:00',
    ) {}

    public function show(): array
    {
        return [
            'sleep' => new ModuleSleepViewModel(
                journalEntry: $this->journalEntry,
            )->sleep($this->startBedTime, $this->startWakeUpTime),
        ];
    }
}
