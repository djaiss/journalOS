<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Actions\ToggleJournalEntryEdition;
use Illuminate\Http\RedirectResponse;

final class JournalEntryToggleController extends Controller
{
    public function edit(Request $request): RedirectResponse
    {
        $journal = $request->attributes->get('journal');
        $journalEntry = $request->attributes->get('journal_entry');

        $updatedEntry = new ToggleJournalEntryEdition(
            user: Auth::user(),
            entry: $journalEntry,
        )->execute();


        if ($updatedEntry->is_edited) {
            $route = 'journal.entry.edit';
        } else {
            $route = 'journal.entry.show';
        }

        return redirect()->route($route, [
            'slug' => $journal->slug,
            'year' => $updatedEntry->year,
            'month' => $updatedEntry->month,
            'day' => $updatedEntry->day,
        ]);
    }
}
