<?php

declare(strict_types=1);

namespace App\Http\Controllers\Llm;

use App\Actions\GetJournalEntriesMarkdownForLLM;
use App\Actions\GetJournalEntryMarkdownForLLM;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

final class JournalEntryController extends Controller
{
    public function showYear(Request $request, string $accessKey, string $year): Response
    {
        $this->ensureValidYear($year);

        $journal = $request->attributes->get('journal');

        try {
            $markdown = (new GetJournalEntriesMarkdownForLLM(
                journal: $journal,
                year: (int) $year,
                month: null,
            ))->execute();
        } catch (ValidationException|ModelNotFoundException) {
            abort(404);
        }

        return response($markdown, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    public function showMonth(Request $request, string $accessKey, string $year, string $month): Response
    {
        $this->ensureValidMonth($year, $month);

        $journal = $request->attributes->get('journal');

        try {
            $markdown = (new GetJournalEntriesMarkdownForLLM(
                journal: $journal,
                year: (int) $year,
                month: (int) $month,
            ))->execute();
        } catch (ValidationException|ModelNotFoundException) {
            abort(404);
        }

        return response($markdown, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    public function show(Request $request, string $accessKey, string $year, string $month, string $day): Response
    {
        $this->ensureValidDate($year, $month, $day);

        $journal = $request->attributes->get('journal');

        try {
            $markdown = (new GetJournalEntryMarkdownForLLM(
                journal: $journal,
                year: (int) $year,
                month: (int) $month,
                day: (int) $day,
            ))->execute();
        } catch (ValidationException|ModelNotFoundException) {
            abort(404);
        }

        return response($markdown, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    private function ensureValidDate(string $year, string $month, string $day): void
    {
        if ($year === '' || ! ctype_digit($year)
            || $month === '' || ! ctype_digit($month)
            || $day === '' || ! ctype_digit($day)) {
            abort(404);
        }

        if (! checkdate((int) $month, (int) $day, (int) $year)) {
            abort(404);
        }
    }

    private function ensureValidMonth(string $year, string $month): void
    {
        if ($year === '' || ! ctype_digit($year)
            || $month === '' || ! ctype_digit($month)) {
            abort(404);
        }

        if (! checkdate((int) $month, 1, (int) $year)) {
            abort(404);
        }
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
