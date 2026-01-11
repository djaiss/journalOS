<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Travel;

use App\Actions\LogTravel;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TravelController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'has_traveled' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:travel_modes'],
            'travel_modes' => ['nullable', 'array', 'min:1', 'required_without_all:has_traveled'],
            'travel_modes.*' => [
                'string',
                'max:255',
                'in:car,plane,train,bike,bus,walk,boat,other',
            ],
        ]);

        $entry = new LogTravel(
            user: $request->user(),
            entry: $journalEntry,
            hasTraveled: array_key_exists('has_traveled', $validated) ? TextSanitizer::nullablePlainText($validated['has_traveled']) : null,
            travelModes: array_key_exists('travel_modes', $validated)
                ? array_map(TextSanitizer::plainText(...), $validated['travel_modes'])
                : null,
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
