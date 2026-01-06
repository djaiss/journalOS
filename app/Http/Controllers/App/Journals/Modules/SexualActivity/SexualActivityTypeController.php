<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\SexualActivity;

use App\Actions\LogSexualActivityType;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class SexualActivityTypeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'sexual_activity_type' => ['required', 'string', 'max:255', 'in:solo,with-partner,intimate-contact'],
        ]);

        new LogSexualActivityType(
            user: Auth::user(),
            entry: $journalEntry,
            sexualActivityType: TextSanitizer::plainText($validated['sexual_activity_type']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
