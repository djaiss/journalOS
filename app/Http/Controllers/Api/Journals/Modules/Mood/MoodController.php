<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Journals\Modules\Mood;

use App\Actions\LogMood;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleMood;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class MoodController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'mood' => ['required', 'string', 'max:255', Rule::in(ModuleMood::MOOD_VALUES)],
        ]);

        $entry = new LogMood(
            user: Auth::user(),
            entry: $entry,
            mood: TextSanitizer::plainText($validated['mood']),
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
