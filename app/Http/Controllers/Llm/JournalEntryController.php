<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Llm;

use App\Actions\GetJournalEntryMarkdownForLLM;
use App\Actions\LogJournalLlmAccess;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

final class JournalEntryController extends Controller
{
    public function show(Request $request, string $accessKey, string $year, string $month, string $day): Response
    {
        $this->ensureValidDate($year, $month, $day);

        $journal = $request->attributes->get('journal');

        new LogJournalLlmAccess(
            journal: $journal,
            requestUrl: $request->fullUrl(),
            year: (int) $year,
            month: (int) $month,
            day: (int) $day,
        )->execute();

        try {
            $markdown = new GetJournalEntryMarkdownForLLM(
                journal: $journal,
                year: (int) $year,
                month: (int) $month,
                day: (int) $day,
            )->execute();
        } catch (ValidationException|ModelNotFoundException) {
            abort(404);
        }

        return response($markdown, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    private function ensureValidDate(string $year, string $month, string $day): void
    {
        if (
            $year === ''
            || !ctype_digit($year)
            || $month === ''
            || !ctype_digit($month)
            || $day === ''
            || !ctype_digit($day)
        ) {
            abort(404);
        }

        if (!checkdate((int) $month, (int) $day, (int) $year)) {
            abort(404);
        }
    }
}
