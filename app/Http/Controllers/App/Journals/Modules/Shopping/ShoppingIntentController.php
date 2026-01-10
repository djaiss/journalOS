<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Shopping;

use App\Actions\LogShoppingIntent;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ShoppingIntentController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'shopping_intent' => ['required', 'string', 'max:255', 'in:planned,opportunistic,impulse,replacement'],
        ]);

        new LogShoppingIntent(
            user: Auth::user(),
            entry: $journalEntry,
            shoppingIntent: TextSanitizer::plainText($validated['shopping_intent']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
