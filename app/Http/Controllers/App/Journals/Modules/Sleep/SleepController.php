<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Sleep;

use App\Http\Controllers\Controller;
use App\View\Presenters\SleepModulePresenter;
use Illuminate\View\View;
use Illuminate\Http\Request;

final class SleepController extends Controller
{
    public function show(Request $request): View
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $bedtime = $request->route()->parameter('bedtime');
        $wake_up_time = $request->route()->parameter('wake_up_time');

        $request->merge([
            'bedtime' => $bedtime,
            'wake_up_time' => $wake_up_time,
        ]);

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

        $module = new SleepModulePresenter($journalEntry)->build(
            bedtime: $validated['bedtime'],
            wake_up_time: $validated['wake_up_time'],
            skipShift: true,
        );

        return view('app.journal.entry.partials.sleep', [
            'module' => $module,
        ]);
    }
}
