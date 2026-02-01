<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Journals;

use App\Actions\CreateJournal;
use App\Actions\DestroyJournal;
use App\Actions\RenameJournal;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalResource;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class JournalController extends Controller
{
    use ApiResponses;

    public function index(): AnonymousResourceCollection
    {
        $journals = Auth::user()
            ->journals()
            ->orderBy('id')
            ->get();

        return JournalResource::collection($journals);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $journal = new CreateJournal(
            user: Auth::user(),
            name: TextSanitizer::plainText($validated['name']),
        )->execute();

        return new JournalResource($journal)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request): JsonResponse
    {
        $journal = $request->attributes->get('journal');

        return new JournalResource($journal)
            ->response()
            ->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $journal = $request->attributes->get('journal');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new RenameJournal(
            user: Auth::user(),
            journal: $journal,
            name: TextSanitizer::plainText($validated['name']),
        )->execute();

        return new JournalResource($journal)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $journal = $request->attributes->get('journal');

        new DestroyJournal(
            user: Auth::user(),
            journal: $journal,
        )->execute();

        return response()->noContent(204);
    }
}
