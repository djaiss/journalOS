<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules;

use App\Http\Controllers\Controller;
use App\Http\ViewModels\Journal\JournalEntryShowViewModel;
use Illuminate\View\View;
use Illuminate\Http\Request;

final class SleepController extends Controller
{
    public function show(Request $request): View
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $data = new JournalEntryShowViewModel(
            journalEntry: $journalEntry,
            startBedTime: $request->route()->parameter('bedtime'),
            startWakeUpTime: $request->route()->parameter('wake_up_time'),
        )->show();

        return view('app.journal.entry.partials.sleep', [
            'data' => $data,
        ]);
    }
}
