<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class JournalLlmAccessLogController extends Controller
{
    public function index(Request $request): View
    {
        $journal = $request->attributes->get('journal');

        $accessLogs = $journal->llmAccessLogs()
            ->latest()
            ->cursorPaginate(10);

        return view('app.journal.settings.llm.logs.index', [
            'journal' => $journal,
            'accessLogs' => $accessLogs,
        ]);
    }
}
