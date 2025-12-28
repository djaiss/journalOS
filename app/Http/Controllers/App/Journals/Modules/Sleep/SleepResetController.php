<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Sleep;

use App\Actions\ResetSleepData;
use App\Http\Controllers\Controller;
use App\View\Presenters\SleepModulePresenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SleepResetController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        new ResetSleepData(
            user: Auth::user(),
            entry: $journalEntry,
        )->execute();

        $module = new SleepModulePresenter($journalEntry)->build();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
            'module' => $module,
        ])->with('status', __('Changes saved'));
    }
}
