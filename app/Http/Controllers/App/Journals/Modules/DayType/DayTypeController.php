<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals\Modules\DayType;

use App\Actions\LogTypeOfDay;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModuleDayType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class DayTypeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'day_type' => ['required', 'string', 'max:255', Rule::in(ModuleDayType::DAY_TYPES)],
        ]);

        new LogTypeOfDay(
            user: Auth::user(),
            entry: $journalEntry,
            dayType: TextSanitizer::plainText($validated['day_type']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
