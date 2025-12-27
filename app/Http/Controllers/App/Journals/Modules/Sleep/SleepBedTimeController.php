<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Sleep;

use App\Actions\LogBedTime;
use App\Http\Controllers\Controller;
use App\View\Presenters\SleepModulePresenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SleepBedTimeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        new LogBedTime(
            user: Auth::user(),
            entry: $journalEntry,
            bedtime: $request->input('bedtime'),
        )->execute();

        $module = new SleepModulePresenter($journalEntry)->build(
            bedtime: $request->input('bedtime'),
            wake_up_time: $journalEntry->wake_up_time ?? '06:00',
        );

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
            'module' => $module,
        ])->with('status', __('Changes saved'));
    }
}
