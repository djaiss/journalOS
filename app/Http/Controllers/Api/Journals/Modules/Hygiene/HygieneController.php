<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Hygiene;

use App\Actions\LogHygiene;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class HygieneController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'showered' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:brushed_teeth,skincare'],
            'brushed_teeth' => ['nullable', 'string', 'max:255', 'in:no,am,pm', 'required_without_all:showered,skincare'],
            'skincare' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:showered,brushed_teeth'],
        ]);

        $entry = new LogHygiene(
            user: Auth::user(),
            entry: $entry,
            showered: array_key_exists('showered', $validated) ? TextSanitizer::plainText($validated['showered']) : null,
            brushedTeeth: array_key_exists('brushed_teeth', $validated) ? TextSanitizer::plainText($validated['brushed_teeth']) : null,
            skincare: array_key_exists('skincare', $validated) ? TextSanitizer::plainText($validated['skincare']) : null,
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
