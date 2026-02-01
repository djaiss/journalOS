<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Journals\Modules\Kids;

use App\Actions\LogHadKidsToday;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class KidsController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'had_kids_today' => ['required', 'string', 'max:255', 'in:yes,no'],
        ]);

        $entry = new LogHadKidsToday(
            user: Auth::user(),
            entry: $journalEntry,
            hadKidsToday: TextSanitizer::plainText($validated['had_kids_today']),
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
