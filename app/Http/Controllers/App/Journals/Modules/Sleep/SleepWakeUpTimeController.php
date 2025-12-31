<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Sleep;

use App\Actions\LogWakeUpTime;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SleepWakeUpTimeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        new LogWakeUpTime(
            user: Auth::user(),
            entry: $journalEntry,
            wakeUpTime: $request->input('wake_up_time'),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
