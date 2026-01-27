<?php

declare(strict_types=1);

namespace App\Http\Controllers\Llm;

use App\Actions\GetJournalEntriesMarkdownForLLM;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

final class JournalEntryMonthController extends Controller
{
    public function index(Request $request, string $accessKey, string $year, string $month): Response
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
}
