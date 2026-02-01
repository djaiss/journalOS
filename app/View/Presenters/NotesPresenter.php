<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use Illuminate\Support\Str;

final readonly class NotesPresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $richText = $this->entry->richTextNotes;
        $notesRendered = $richText ? mb_trim($richText->render()) : '';
        $hasNotes = $richText ? $richText->toPlainText() !== '' : false;

        return [
            'notes_edit_url' => route('journal.entry.notes.edit', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.notes.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'notes_markdown' => $hasNotes ? Str::markdown($notesRendered) : null,
            'display_reset' => $hasNotes,
        ];
    }
}
