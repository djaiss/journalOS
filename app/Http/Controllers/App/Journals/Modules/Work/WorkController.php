<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Work;

use App\Actions\LogHasWorked;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class WorkController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'worked' => ['required', 'string', 'in:yes,no'],
        ]);

        new LogHasWorked(
            user: Auth::user(),
            entry: $journalEntry,
            hasWorked: $request->input('worked'),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
