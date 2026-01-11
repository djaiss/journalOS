<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\SexualActivity;

use App\Actions\LogSexualActivity;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SexualActivityController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'had_sexual_activity' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:sexual_activity_type'],
            'sexual_activity_type' => ['nullable', 'string', 'max:255', 'in:solo,with-partner,intimate-contact', 'required_without_all:had_sexual_activity'],
        ]);

        $entry = new LogSexualActivity(
            user: $request->user(),
            entry: $journalEntry,
            hadSexualActivity: array_key_exists('had_sexual_activity', $validated)
                ? TextSanitizer::nullablePlainText($validated['had_sexual_activity'])
                : null,
            sexualActivityType: array_key_exists('sexual_activity_type', $validated)
                ? TextSanitizer::nullablePlainText($validated['sexual_activity_type'])
                : null,
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
