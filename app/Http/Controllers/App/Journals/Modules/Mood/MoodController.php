<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Mood;

use App\Actions\LogMood;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class MoodController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'mood' => ['required', 'string', 'max:255', 'in:terrible,bad,okay,good,great'],
        ]);

        new LogMood(
            user: Auth::user(),
            entry: $entry,
            mood: TextSanitizer::plainText($validated['mood']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
