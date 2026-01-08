<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Notes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class NotesController extends Controller
{
    public function create(Request $request): View
    {
        $journal = $request->attributes->get('journal');
        $journalEntry = $request->attributes->get('journal_entry');

        return view('app.journal.entry.notes.create', [
            'journal' => $journal,
            'entry' => $journalEntry,
        ]);
    }
}
