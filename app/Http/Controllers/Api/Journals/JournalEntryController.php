<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals;

use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class JournalEntryController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');

        return new JournalEntryResource($journalEntry)
            ->response()
            ->setStatusCode(200);
    }
}
