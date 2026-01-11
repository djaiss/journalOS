<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\SexualActivity;

use App\Actions\LogSexualActivity;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModuleSexualActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class SexualActivityController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'had_sexual_activity' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:sexual_activity_type'],
            'sexual_activity_type' => ['nullable', 'string', 'max:255', Rule::in(ModuleSexualActivity::SEXUAL_ACTIVITY_TYPES), 'required_without_all:had_sexual_activity'],
        ]);

        new LogSexualActivity(
            user: Auth::user(),
            entry: $journalEntry,
            hadSexualActivity: array_key_exists('had_sexual_activity', $validated)
                ? TextSanitizer::plainText($validated['had_sexual_activity'])
                : null,
            sexualActivityType: array_key_exists('sexual_activity_type', $validated)
                ? TextSanitizer::plainText($validated['sexual_activity_type'])
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
