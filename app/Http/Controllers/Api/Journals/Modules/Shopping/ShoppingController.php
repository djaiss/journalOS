<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Shopping;

use App\Actions\LogShoppedToday;
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
            'has_shopped' => ['required', 'string', 'max:255', 'in:yes,no'],
        ]);

        $entry = new LogShoppedToday(
            user: Auth::user(),
            entry: $entry,
            hasShopped: TextSanitizer::plainText($validated['has_shopped']),
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
