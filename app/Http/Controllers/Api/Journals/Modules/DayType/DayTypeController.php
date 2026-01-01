<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\DayType;

use App\Actions\LogTypeOfDay;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DayTypeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'day_type' => ['required', 'string', 'in:workday,day off,weekend,vacation,sick day'],
        ]);

        $entry = new LogTypeOfDay(
            user: $request->user(),
            entry: $journalEntry,
            dayType: $validated['day_type'],
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
