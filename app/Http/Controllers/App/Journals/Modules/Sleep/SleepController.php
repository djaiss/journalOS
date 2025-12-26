<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Sleep;

use App\Helpers\Modules\Sleep\SleepHelper;
use App\Http\Controllers\Controller;
use App\Http\ViewModels\Journal\JournalEntryShowViewModel;
use Illuminate\View\View;
use Illuminate\Http\Request;

final class SleepController extends Controller
{
    public function show(Request $request): View
    {
        $journal = $request->attributes->get('journal');
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

        $bedtimeRange = SleepHelper::range($validated['bedtime'], $journalEntry->bedtime);
        $wakeUpRange = SleepHelper::range($validated['wake_up_time'], $journalEntry->wake_up_time);

        $previousBedtimeUrl = route('journal.entry.sleep.show', [
            'slug' => $journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
            'bedtime' => SleepHelper::shift($validated['bedtime'], -5),
            'wake_up_time' => $validated['wake_up_time'],
        ]);

        $nextBedtimeUrl = route('journal.entry.sleep.show', [
            'slug' => $journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
            'bedtime' => SleepHelper::shift($validated['bedtime'], +5),
            'wake_up_time' => $validated['wake_up_time'],
        ]);

        $previousWakeUpUrl = route('journal.entry.sleep.show', [
            'slug' => $journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
            'bedtime' => $validated['bedtime'],
            'wake_up_time' => SleepHelper::shift($validated['wake_up_time'], -5),
        ]);

        $nextWakeUpUrl = route('journal.entry.sleep.show', [
            'slug' => $journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
            'bedtime' => $validated['bedtime'],
            'wake_up_time' => SleepHelper::shift($validated['wake_up_time'], +5),
        ]);

        return view('app.journal.entry.partials.sleep', [
            'bedtimeRange' => $bedtimeRange,
            'wakeUpRange' => $wakeUpRange,
            'previousBedtimeUrl' => $previousBedtimeUrl,
            'nextBedtimeUrl' => $nextBedtimeUrl,
            'previousWakeUpUrl' => $previousWakeUpUrl,
            'nextWakeUpUrl' => $nextWakeUpUrl,
        ]);
    }
}
