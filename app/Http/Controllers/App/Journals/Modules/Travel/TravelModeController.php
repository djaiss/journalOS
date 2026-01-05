<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Travel;

use App\Actions\LogTravelMode;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class TravelModeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'travel_modes' => ['required', 'array', 'min:1'],
            'travel_modes.*' => ['required', 'string', 'max:255', 'in:car,plane,train,bike,bus,walk,boat,other'],
        ]);

        new LogTravelMode(
            user: Auth::user(),
            entry: $journalEntry,
            travelModes: $validated['travel_modes'],
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
