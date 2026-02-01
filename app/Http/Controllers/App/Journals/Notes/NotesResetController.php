<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals\Notes;

use App\Actions\ResetNotes;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class NotesResetController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        new ResetNotes(
            user: Auth::user(),
            entry: $journalEntry,
        )->execute();

        return to_route('journal.entry.edit', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
