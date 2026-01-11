<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Shopping;

use App\Actions\LogShopping;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ShoppingController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'has_shopped' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:shopping_types,shopping_intent,shopping_context,shopping_for'],
            'shopping_types' => ['nullable', 'array', 'min:1', 'required_without_all:has_shopped,shopping_intent,shopping_context,shopping_for'],
            'shopping_types.*' => [
                'string',
                'max:255',
                'in:groceries,clothes,electronics_tech,household_essentials,books_media,gifts,online_shopping,other',
            ],
            'shopping_intent' => ['nullable', 'string', 'max:255', 'in:planned,opportunistic,impulse,replacement', 'required_without_all:has_shopped,shopping_types,shopping_context,shopping_for'],
            'shopping_context' => ['nullable', 'string', 'max:255', 'in:alone,with_partner,with_kids', 'required_without_all:has_shopped,shopping_types,shopping_intent,shopping_for'],
            'shopping_for' => ['nullable', 'string', 'max:255', 'in:for_self,for_household,for_others', 'required_without_all:has_shopped,shopping_types,shopping_intent,shopping_context'],
        ]);

        $entry = new LogShopping(
            user: Auth::user(),
            entry: $entry,
            hasShopped: array_key_exists('has_shopped', $validated) ? TextSanitizer::nullablePlainText($validated['has_shopped']) : null,
            shoppingTypes: array_key_exists('shopping_types', $validated)
                ? array_map(TextSanitizer::plainText(...), $validated['shopping_types'])
                : null,
            shoppingIntent: array_key_exists('shopping_intent', $validated) ? TextSanitizer::nullablePlainText($validated['shopping_intent']) : null,
            shoppingContext: array_key_exists('shopping_context', $validated) ? TextSanitizer::nullablePlainText($validated['shopping_context']) : null,
            shoppingFor: array_key_exists('shopping_for', $validated) ? TextSanitizer::nullablePlainText($validated['shopping_for']) : null,
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
