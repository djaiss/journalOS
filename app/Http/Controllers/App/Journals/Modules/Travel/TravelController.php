<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Travel;

use App\Actions\LogTravel;
use App\Helpers\TextSanitizer;
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
            'has_traveled' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:travel_modes'],
            'travel_modes' => ['nullable', 'array', 'min:1', 'required_without_all:has_traveled'],
            'travel_modes.*' => [
                'string',
                'max:255',
                'in:car,plane,train,bike,bus,walk,boat,other',
            ],
        ]);

        new LogTravel(
            user: Auth::user(),
            entry: $journalEntry,
            hasTraveled: array_key_exists('has_traveled', $validated) ? TextSanitizer::plainText($validated['has_traveled']) : null,
            travelModes: array_key_exists('travel_modes', $validated)
                ? array_map(TextSanitizer::plainText(...), $validated['travel_modes'])
                : null,
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
