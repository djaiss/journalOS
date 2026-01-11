<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Sleep;

use App\Actions\LogSleep;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\View\Presenters\SleepModulePresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class SleepController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'bedtime' => ['nullable', 'date_format:H:i', 'required_without_all:wake_up_time'],
            'wake_up_time' => ['nullable', 'date_format:H:i', 'required_without_all:bedtime'],
        ]);

        new LogSleep(
            user: Auth::user(),
            entry: $journalEntry,
            bedtime: array_key_exists('bedtime', $validated)
                ? TextSanitizer::plainText($validated['bedtime'])
                : null,
            wakeUpTime: array_key_exists('wake_up_time', $validated)
                ? TextSanitizer::plainText($validated['wake_up_time'])
                : null,
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }

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
