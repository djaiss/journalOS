<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Sleep;

use App\Actions\LogWakeUpTime;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SleepWakeUpTimeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'wake_up_time' => ['required', 'date_format:H:i'],
        ]);

        $entry = new LogWakeUpTime(
            user: $request->user(),
            entry: $journalEntry,
            wakeUpTime: $validated['wake_up_time'],
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
