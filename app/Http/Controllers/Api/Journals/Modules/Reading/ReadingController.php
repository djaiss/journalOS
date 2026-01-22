<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Reading;

use App\Actions\LogReading;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class ReadingController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'did_read_today' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:reading_amount,mental_state,reading_feel,want_continue,reading_limit'],
            'reading_amount' => ['nullable', 'string', 'max:255', Rule::in(ModuleReading::READING_AMOUNTS), 'required_without_all:did_read_today,mental_state,reading_feel,want_continue,reading_limit'],
            'mental_state' => ['nullable', 'string', 'max:255', Rule::in(ModuleReading::MENTAL_STATES), 'required_without_all:did_read_today,reading_amount,reading_feel,want_continue,reading_limit'],
            'reading_feel' => ['nullable', 'string', 'max:255', Rule::in(ModuleReading::READING_FEELS), 'required_without_all:did_read_today,reading_amount,mental_state,want_continue,reading_limit'],
            'want_continue' => ['nullable', 'string', 'max:255', Rule::in(ModuleReading::WANT_CONTINUE_OPTIONS), 'required_without_all:did_read_today,reading_amount,mental_state,reading_feel,reading_limit'],
            'reading_limit' => ['nullable', 'string', 'max:255', Rule::in(ModuleReading::READING_LIMITS), 'required_without_all:did_read_today,reading_amount,mental_state,reading_feel,want_continue'],
        ]);

        $entry = (new LogReading(
            user: Auth::user(),
            entry: $entry,
            didReadToday: array_key_exists('did_read_today', $validated) ? TextSanitizer::plainText($validated['did_read_today']) : null,
            readingAmount: array_key_exists('reading_amount', $validated) ? TextSanitizer::plainText($validated['reading_amount']) : null,
            mentalState: array_key_exists('mental_state', $validated) ? TextSanitizer::plainText($validated['mental_state']) : null,
            readingFeel: array_key_exists('reading_feel', $validated) ? TextSanitizer::plainText($validated['reading_feel']) : null,
            wantContinue: array_key_exists('want_continue', $validated) ? TextSanitizer::plainText($validated['want_continue']) : null,
            readingLimit: array_key_exists('reading_limit', $validated) ? TextSanitizer::plainText($validated['reading_limit']) : null,
        ))->execute();

        $entry->load('moduleReading', 'books');

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
