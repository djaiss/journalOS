<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Sleep;

use App\Actions\LogBedTime;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SleepBedTimeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'bedtime' => ['required', 'date_format:H:i'],
        ]);

        $entry = new LogBedTime(
            user: $request->user(),
            entry: $journalEntry,
            bedtime: TextSanitizer::plainText($validated['bedtime']),
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
