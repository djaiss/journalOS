<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Services\CreateJournal;
use App\Services\DestroyJournal;
use App\Services\UpdateJournal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Journals
 */
class JournalController extends Controller
{
    /**
     * Create a journal
     *
     * @bodyParam name string required The name of the journal. Max 255 characters. Example: New journal
     *
     * @response 201 {
     *  "id": 4,
     *  "object": "journal",
     *  "label": "New journal",
     * }
     */
    public function create(Request $request): JsonResponse
    {
        $journal = (new CreateJournal(
            name: $request->input('name'),
        ))->execute();

        return response()->json([
            'id' => $journal->id,
            'object' => 'journal',
            'name' => $journal->name,
        ], 201);
    }

    /**
     * Update a journal
     *
     * @urlParam journal required The id of the journal. Example: 1
     *
     * @bodyParam name string required The name of the journal. Max 255 characters. Example: New journal
     *
     * @response 200 {
     *  "id": 4,
     *  "object": "journal",
     *  "label": "New journal",
     * }
     */
    public function update(Request $request, int $journalId): JsonResponse
    {
        $journal = Journal::where('user_id', auth()->user()->id)
            ->findOrFail($journalId);

        $journal = (new UpdateJournal(
            journal: $journal,
            name: $request->input('name'),
        ))->execute();

        return response()->json([
            'id' => $journal->id,
            'object' => 'journal',
            'name' => $journal->name,
        ], 200);
    }

    /**
     * Delete a journal
     *
     * @urlParam journal required The id of the journal. Example: 1
     *
     * @response 200 {
     *  "status": "success",
     * }
     */
    public function destroy(Request $request, int $journalId): JsonResponse
    {
        $journal = Journal::where('user_id', auth()->user()->id)
            ->findOrFail($journalId);

        (new DestroyJournal(
            journal: $journal,
        ))->execute();

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     * List all journals
     *
     * This will list all the journals, sorted
     * alphabetically.
     *
     * @response 200 [{
     *  "id": 4,
     *  "object": "journal",
     *  "label": "New journal",
     * }, {
     *  "id": 5,
     *  "object": "journal",
     *  "label": "Old journal",
     * }]
     */
    public function index(): JsonResponse
    {
        $journals = auth()->user()->journals()
            ->orderBy('name')
            ->get()
            ->map(fn (Journal $journal) => [
                'id' => $journal->id,
                'object' => 'journal',
                'name' => $journal->name,
            ]);

        return response()->json($journals, 200);
    }
}
