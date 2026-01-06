<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Work;

use App\Actions\LogHasWorked;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WorkController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'worked' => ['required', 'string', 'max:255', 'in:yes,no'],
        ]);

        $entry = new LogHasWorked(
            user: $request->user(),
            entry: $journalEntry,
            hasWorked: TextSanitizer::plainText($validated['worked']),
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
