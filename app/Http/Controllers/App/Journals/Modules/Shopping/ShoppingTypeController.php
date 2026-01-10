<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Shopping;

use App\Actions\LogShoppingType;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ShoppingTypeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'shopping_types' => ['required', 'array', 'min:1'],
            'shopping_types.*' => [
                'required',
                'string',
                'max:255',
                'in:groceries,clothes,electronics_tech,household_essentials,books_media,gifts,online_shopping,other',
            ],
        ]);

        new LogShoppingType(
            user: Auth::user(),
            entry: $journalEntry,
            shoppingTypes: array_map(
                TextSanitizer::plainText(...),
                $validated['shopping_types'],
            ),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
