<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\SexualActivity;

use App\Actions\LogHadSexualActivity;
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
            'had_sexual_activity' => ['required', 'string', 'in:yes,no'],
        ]);

        $entry = new LogHadSexualActivity(
            user: $request->user(),
            entry: $journalEntry,
            hadSexualActivity: $validated['had_sexual_activity'],
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
