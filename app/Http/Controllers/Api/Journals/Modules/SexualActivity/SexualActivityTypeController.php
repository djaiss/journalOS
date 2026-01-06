<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\SexualActivity;

use App\Actions\LogSexualActivityType;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SexualActivityTypeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'sexual_activity_type' => ['required', 'string', 'max:255', 'in:solo,with-partner,intimate-contact'],
        ]);

        $entry = new LogSexualActivityType(
            user: $request->user(),
            entry: $journalEntry,
            sexualActivityType: TextSanitizer::plainText($validated['sexual_activity_type']),
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
