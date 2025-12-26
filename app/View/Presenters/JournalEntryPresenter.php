<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class JournalEntryPresenter
{
    public function __construct(private JournalEntry $entry) {}

    public function build(): array
    {
        $sleep = (new SleepModulePresenter($this->entry))
            ->build('20:00', '06:00');

        return [
            'sleep' => $sleep,
        ];
    }
}
