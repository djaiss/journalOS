<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\CognitiveLoad;

use App\Actions\LogCognitiveLoad;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleCognitiveLoad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class CognitiveLoadController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'cognitive_load' => ['required', 'string', 'max:255', Rule::in(ModuleCognitiveLoad::COGNITIVE_LOAD_LEVELS)],
            'primary_source' => ['nullable', 'string', 'max:255', Rule::in(ModuleCognitiveLoad::PRIMARY_SOURCES)],
            'load_quality' => ['nullable', 'string', 'max:255', Rule::in(ModuleCognitiveLoad::LOAD_QUALITIES)],
        ]);

        $entry = new LogCognitiveLoad(
            user: Auth::user(),
            entry: $entry,
            cognitiveLoad: TextSanitizer::plainText($validated['cognitive_load']),
            primarySource: array_key_exists('primary_source', $validated) && $validated['primary_source'] !== null
                ? TextSanitizer::plainText($validated['primary_source'])
                : null,
            loadQuality: array_key_exists('load_quality', $validated) && $validated['load_quality'] !== null
                ? TextSanitizer::plainText($validated['load_quality'])
                : null,
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
