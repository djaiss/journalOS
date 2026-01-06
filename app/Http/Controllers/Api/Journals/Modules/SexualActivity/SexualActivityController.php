<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\SexualActivity;

use App\Actions\LogHadSexualActivity;
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
            'had_sexual_activity' => ['required', 'string', 'max:255', 'in:yes,no'],
        ]);

        $entry = new LogHadSexualActivity(
            user: $request->user(),
            entry: $journalEntry,
            hadSexualActivity: TextSanitizer::plainText($validated['had_sexual_activity']),
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
