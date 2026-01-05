<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Travel;

use App\Actions\LogTravelMode;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TravelModeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'travel_modes' => ['required', 'array', 'min:1'],
            'travel_modes.*' => ['required', 'string', 'max:255', 'in:car,plane,train,bike,bus,walk,boat,other'],
        ]);

        $entry = new LogTravelMode(
            user: $request->user(),
            entry: $journalEntry,
            travelModes: $validated['travel_modes'],
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
