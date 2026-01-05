<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\PrimaryObligation;

use App\Actions\LogPrimaryObligation;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class PrimaryObligationController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'primary_obligation' => ['required', 'string', 'max:255', 'in:work,family,personal,health,travel,none'],
        ]);

        $entry = new LogPrimaryObligation(
            user: Auth::user(),
            entry: $entry,
            primaryObligation: $validated['primary_obligation'],
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
