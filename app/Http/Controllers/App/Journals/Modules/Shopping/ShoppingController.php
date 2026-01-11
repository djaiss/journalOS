<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Shopping;

use App\Actions\LogShopping;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModuleShopping;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class ShoppingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'has_shopped' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:shopping_types,shopping_intent,shopping_context,shopping_for'],
            'shopping_types' => ['nullable', 'array', 'min:1', 'required_without_all:has_shopped,shopping_intent,shopping_context,shopping_for'],
            'shopping_types.*' => [
                'string',
                'max:255',
                Rule::in(ModuleShopping::SHOPPING_TYPES),
            ],
            'shopping_intent' => ['nullable', 'string', 'max:255', Rule::in(ModuleShopping::SHOPPING_INTENTS), 'required_without_all:has_shopped,shopping_types,shopping_context,shopping_for'],
            'shopping_context' => ['nullable', 'string', 'max:255', Rule::in(ModuleShopping::SHOPPING_CONTEXTS), 'required_without_all:has_shopped,shopping_types,shopping_intent,shopping_for'],
            'shopping_for' => ['nullable', 'string', 'max:255', Rule::in(ModuleShopping::SHOPPING_FOR_OPTIONS), 'required_without_all:has_shopped,shopping_types,shopping_intent,shopping_context'],
        ]);

        new LogShopping(
            user: Auth::user(),
            entry: $journalEntry,
            hasShopped: array_key_exists('has_shopped', $validated) ? TextSanitizer::plainText($validated['has_shopped']) : null,
            shoppingTypes: array_key_exists('shopping_types', $validated)
                ? array_map(TextSanitizer::plainText(...), $validated['shopping_types'])
                : null,
            shoppingIntent: array_key_exists('shopping_intent', $validated) ? TextSanitizer::plainText($validated['shopping_intent']) : null,
            shoppingContext: array_key_exists('shopping_context', $validated) ? TextSanitizer::plainText($validated['shopping_context']) : null,
            shoppingFor: array_key_exists('shopping_for', $validated) ? TextSanitizer::plainText($validated['shopping_for']) : null,
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $journalEntry->journal->slug,
            'year' => $journalEntry->year,
            'month' => $journalEntry->month,
            'day' => $journalEntry->day,
        ])->with('status', __('Changes saved'));
    }
}
