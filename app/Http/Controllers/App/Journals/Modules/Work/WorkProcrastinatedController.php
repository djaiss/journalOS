<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Work;

use App\Actions\LogWorkProcrastinated;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class WorkProcrastinatedController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'work_procrastinated' => ['required', 'string', 'max:255', 'in:yes,no'],
        ]);

        new LogWorkProcrastinated(
            user: Auth::user(),
            entry: $journalEntry,
            workProcrastinated: TextSanitizer::plainText($validated['work_procrastinated']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
