<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals;

use App\Actions\ResetDayData;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class JournalEntryResetController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        new ResetDayData(
            user: Auth::user(),
            entry: $journalEntry,
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
