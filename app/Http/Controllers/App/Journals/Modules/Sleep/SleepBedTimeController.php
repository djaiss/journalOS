<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Sleep;

use App\Actions\LogSleep;
use App\Http\Controllers\Controller;
use App\Http\ViewModels\Journal\JournalEntryShowViewModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

final class SleepBedTimeController extends Controller
{
    public function show(Request $request): View
    {
        $journalEntry = $request->attributes->get('journal_entry');

        (new LogSleep(
            user: Auth::user(),
            entry: $journalEntry,
            bedtime: $request->input('bedtime'),
            wakeUpTime: $request->input('wake_up_time'),
        ))->execute();

        return view('app.journal.entry.partials.sleep', [
            'data' => $data,
        ]);
    }
}
