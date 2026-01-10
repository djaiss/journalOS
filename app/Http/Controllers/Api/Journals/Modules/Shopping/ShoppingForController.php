<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Shopping;

use App\Actions\LogShoppingFor;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ShoppingForController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'shopping_for' => ['required', 'string', 'max:255', 'in:for_self,for_household,for_others'],
        ]);

        $entry = new LogShoppingFor(
            user: Auth::user(),
            entry: $entry,
            shoppingFor: TextSanitizer::plainText($validated['shopping_for']),
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
