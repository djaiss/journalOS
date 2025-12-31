<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Work;

use App\Actions\LogWorkMode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Journals\Modules\Work\UpdateWorkModeRequest;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;

final class WorkModeController extends Controller
{
    public function update(UpdateWorkModeRequest $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validated();

        $entry = new LogWorkMode(
            user: $request->user(),
            entry: $journalEntry,
            workMode: $validated['work_mode'],
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
