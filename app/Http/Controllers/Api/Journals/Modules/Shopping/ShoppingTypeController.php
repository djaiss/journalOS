<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Shopping;

use App\Actions\LogShoppingType;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ShoppingTypeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'shopping_types' => ['required', 'array', 'min:1'],
            'shopping_types.*' => [
                'required',
                'string',
                'max:255',
                'in:groceries,clothes,electronics_tech,household_essentials,books_media,gifts,online_shopping,other',
            ],
        ]);

        $entry = new LogShoppingType(
            user: Auth::user(),
            entry: $entry,
            shoppingTypes: array_map(
                TextSanitizer::plainText(...),
                $validated['shopping_types'],
            ),
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
