<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals\Modules\Work;

use App\Actions\LogWork;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModuleWork;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class WorkController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'worked' => [
                'nullable',
                'string',
                'max:255',
                'in:yes,no',
                'required_without_all:work_mode,work_load,work_procrastinated',
            ],
            'work_mode' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleWork::WORK_MODES),
                'required_without_all:worked,work_load,work_procrastinated',
            ],
            'work_load' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleWork::WORK_LOADS),
                'required_without_all:worked,work_mode,work_procrastinated',
            ],
            'work_procrastinated' => [
                'nullable',
                'string',
                'max:255',
                'in:yes,no',
                'required_without_all:worked,work_mode,work_load',
            ],
        ]);

        new LogWork(
            user: Auth::user(),
            entry: $journalEntry,
            worked: array_key_exists('worked', $validated) ? TextSanitizer::plainText($validated['worked']) : null,
            workMode: array_key_exists('work_mode', $validated)
                ? TextSanitizer::plainText($validated['work_mode'])
                : null,
            workLoad: array_key_exists('work_load', $validated)
                ? TextSanitizer::plainText($validated['work_load'])
                : null,
            workProcrastinated: array_key_exists('work_procrastinated', $validated)
                ? TextSanitizer::plainText($validated['work_procrastinated'])
                : null,
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
