<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\SexualActivity;

use App\Actions\LogHadSexualActivity;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class SexualActivityController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'had_sexual_activity' => ['required', 'string', 'max:255', 'in:yes,no'],
        ]);

        new LogHadSexualActivity(
            user: Auth::user(),
            entry: $journalEntry,
            hadSexualActivity: $validated['had_sexual_activity'],
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
