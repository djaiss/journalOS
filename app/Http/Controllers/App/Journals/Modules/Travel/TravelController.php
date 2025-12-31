<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Travel;

use App\Actions\LogTravelledToday;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class TravelController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'has_traveled' => ['required', 'string', 'in:yes,no'],
        ]);

        (new LogTravelledToday(
            user: Auth::user(),
            entry: $journalEntry,
            hasTraveled: $validated['has_traveled'],
        ))->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
