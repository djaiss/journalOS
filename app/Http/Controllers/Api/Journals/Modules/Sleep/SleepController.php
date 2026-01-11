<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Sleep;

use App\Actions\LogSleep;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SleepController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'bedtime' => ['nullable', 'date_format:H:i', 'required_without_all:wake_up_time'],
            'wake_up_time' => ['nullable', 'date_format:H:i', 'required_without_all:bedtime'],
        ]);

        $entry = new LogSleep(
            user: $request->user(),
            entry: $journalEntry,
            bedtime: array_key_exists('bedtime', $validated)
                ? TextSanitizer::nullablePlainText($validated['bedtime'])
                : null,
            wakeUpTime: array_key_exists('wake_up_time', $validated)
                ? TextSanitizer::nullablePlainText($validated['wake_up_time'])
                : null,
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
