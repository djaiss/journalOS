<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class NotesPresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {

    }
}
