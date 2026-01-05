<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Travel;

use App\Actions\LogTravelledToday;
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
            'has_traveled' => ['required', 'string', 'max:255', 'in:yes,no'],
        ]);

        $entry = new LogTravelledToday(
            user: $request->user(),
            entry: $journalEntry,
            hasTraveled: $validated['has_traveled'],
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
