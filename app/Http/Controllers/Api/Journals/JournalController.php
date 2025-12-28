<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals;

use App\Actions\CreateJournal;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalResource;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class JournalController extends Controller
{
    use ApiResponses;

    public function index(): AnonymousResourceCollection
    {
        $journals = Auth::user()->journals()->orderBy('id')->get();

        return JournalResource::collection($journals);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $journal = new CreateJournal(
            user: Auth::user(),
            name: $validated['name'],
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
}
