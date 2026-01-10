<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Shopping;

use App\Actions\LogShoppingIntent;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ShoppingIntentController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'shopping_intent' => ['required', 'string', 'max:255', 'in:planned,opportunistic,impulse,replacement'],
        ]);

        $entry = new LogShoppingIntent(
            user: Auth::user(),
            entry: $entry,
            shoppingIntent: TextSanitizer::plainText($validated['shopping_intent']),
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
