<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Work;

use App\Actions\LogWorkProcrastinated;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WorkProcrastinatedController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'work_procrastinated' => ['required', 'string', 'in:yes,no'],
        ]);

        $entry = new LogWorkProcrastinated(
            user: $request->user(),
            entry: $journalEntry,
            workProcrastinated: $validated['work_procrastinated'],
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
