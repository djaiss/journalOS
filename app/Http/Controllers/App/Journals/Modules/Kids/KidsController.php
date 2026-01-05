<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Kids;

use App\Actions\LogHadKidsToday;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class KidsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'had_kids_today' => ['required', 'string', 'max:255', 'in:yes,no'],
        ]);

        new LogHadKidsToday(
            user: Auth::user(),
            entry: $journalEntry,
            hadKidsToday: $validated['had_kids_today'],
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
