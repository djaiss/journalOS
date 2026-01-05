<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Work;

use App\Actions\LogWorkLoad;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WorkLoadController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'work_load' => ['required', 'string', 'max:255', 'in:light,medium,heavy'],
        ]);

        $entry = new LogWorkLoad(
            user: $request->user(),
            entry: $journalEntry,
            workLoad: $validated['work_load'],
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
