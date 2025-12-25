<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Sleep;

use App\Http\Controllers\Controller;
use App\Http\ViewModels\Journal\JournalEntryShowViewModel;
use Illuminate\View\View;
use Illuminate\Http\Request;

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

        //$journalEntry->bedtime = $validated['bedtime'];
        //$journalEntry->save();

        $data = new JournalEntryShowViewModel(
            journalEntry: $journalEntry,
            startBedTime: $validated['bedtime'],
            startWakeUpTime: $validated['wake_up_time'],
        )->show();
dd($data);
        return view('app.journal.entry.partials.sleep', [
            'data' => $data,
        ]);
    }
}
