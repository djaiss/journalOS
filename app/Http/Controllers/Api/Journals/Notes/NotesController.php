<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Notes;

use App\Actions\LogNotes;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class NotesController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'notes' => ['required', 'string'],
        ]);

        $entry = new LogNotes(
            user: Auth::user(),
            entry: $entry,
            notes: $validated['notes'],
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
