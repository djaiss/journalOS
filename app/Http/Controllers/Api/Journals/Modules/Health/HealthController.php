<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Health;

use App\Actions\LogHealth;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class HealthController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'health' => ['required', 'string', 'in:good,okay,not great'],
        ]);

        $entry = new LogHealth(
            user: Auth::user(),
            entry: $entry,
            health: $validated['health'],
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
