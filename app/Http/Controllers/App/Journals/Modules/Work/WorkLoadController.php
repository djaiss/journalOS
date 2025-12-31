<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Work;

use App\Actions\LogWorkLoad;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class WorkLoadController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'work_load' => ['required', 'string', 'in:light,medium,heavy'],
        ]);

        new LogWorkLoad(
            user: Auth::user(),
            entry: $journalEntry,
            workLoad: $validated['work_load'],
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
