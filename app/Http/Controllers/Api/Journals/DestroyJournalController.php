<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals;

use App\Actions\DestroyJournal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class DestroyJournalController extends Controller
{
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
