<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Sleep;

use App\Helpers\Modules\Sleep\SleepHelper;
use App\Http\Controllers\Controller;
use App\Http\ViewModels\Journal\JournalEntryShowViewModel;
use App\View\Presenters\SleepModulePresenter;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class SleepController extends Controller
{
    public function show(Request $request): View
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'bedtime' => [
                'required',
                'regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/',
            ],
            'wake_up_time' => [
                'required',
                'regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/',
            ],
        ]);

        $module = (new SleepModulePresenter($journalEntry))->build(
            bedtime: $validated['bedtime'],
            wake_up_time: $validated['wake_up_time'],
        );

        return view('app.journal.entry.partials.sleep', [
            'module' => $module,
        ]);
    }
}
