<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Shopping;

use App\Actions\LogShoppingContext;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ShoppingContextController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'shopping_context' => ['required', 'string', 'max:255', 'in:alone,with_partner,with_kids'],
        ]);

        new LogShoppingContext(
            user: Auth::user(),
            entry: $journalEntry,
            shoppingContext: TextSanitizer::plainText($validated['shopping_context']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
