<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals\Notes;

use App\Actions\LogNotes;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class NotesController extends Controller
{
    public function edit(Request $request): View
    {
        $journal = $request->attributes->get('journal');
        $journalEntry = $request->attributes->get('journal_entry');

        return view('app.journal.entry.notes.edit', [
            'journal' => $journal,
            'entry' => $journalEntry,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:100000'],
        ]);

        new LogNotes(
            user: Auth::user(),
            entry: $entry,
            notes: $validated['notes'],
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
