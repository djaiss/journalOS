<?php

declare(strict_types=1);

namespace App\Http\Controllers\Llm;

use App\Actions\GetJournalEntriesMarkdownForLLM;
use App\Actions\LogJournalLlmAccess;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

final class JournalEntryYearController extends Controller
{
    public function index(Request $request, string $accessKey, string $year): Response
    {
        $this->ensureValidYear($year);

        $journal = $request->attributes->get('journal');

        new LogJournalLlmAccess(
            journal: $journal,
            requestUrl: $request->fullUrl(),
            year: (int) $year,
            month: null,
            day: null,
        )->execute();

        try {
            $markdown = new GetJournalEntriesMarkdownForLLM(
                journal: $journal,
                year: (int) $year,
                month: null,
            )->execute();
        } catch (ValidationException|ModelNotFoundException) {
            abort(404);
        }

        return response($markdown, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    private function ensureValidYear(string $year): void
    {
        if ($year === '' || ! ctype_digit($year)) {
            abort(404);
        }

        if (! checkdate(1, 1, (int) $year)) {
            abort(404);
        }
    }
}
