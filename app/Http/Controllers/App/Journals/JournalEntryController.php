<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals;

use App\Helpers\JournalHelper;
use App\Http\Controllers\Controller;
use App\View\Presenters\JournalEntryPresenter;
use App\View\Presenters\JournalEntryShowPresenter;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class JournalEntryController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $journal = $request->attributes->get('journal');
        $journalEntry = $request->attributes->get('journal_entry');

        if ($journalEntry->is_edited) {
            return to_route('journal.entry.edit', [
                'slug' => $journalEntry->journal->slug,
                'year' => $journalEntry->year,
                'month' => $journalEntry->month,
                'day' => $journalEntry->day,
            ]);
        }

        $years = JournalHelper::getYears(
            journal: $journal,
            selectedYear: $journalEntry->year,
        );

        $months = JournalHelper::getMonths(
            journal: $journal,
            year: $journalEntry->year,
            selectedMonth: $journalEntry->month,
        );

        $days = JournalHelper::getDaysInMonth(
            journal: $journal,
            year: $journalEntry->year,
            month: $journalEntry->month,
            day: $journalEntry->day,
        );

        $payload = new JournalEntryShowPresenter($journalEntry)->build();

        return view('app.journal.entry.show', [
            'journal' => $journal,
            'entry' => $journalEntry,
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'entryDate' => $payload['date'],
            'notesMarkdown' => $payload['notes_markdown'],
            'modules' => $payload['modules'],
        ]);
    }

    public function edit(Request $request): View
    {
        $journal = $request->attributes->get('journal');
        $journalEntry = $request->attributes->get('journal_entry');

        $years = JournalHelper::getYears(
            journal: $journal,
            selectedYear: $journalEntry->year,
        );

        $months = JournalHelper::getMonths(
            journal: $journal,
            year: $journalEntry->year,
            selectedMonth: $journalEntry->month,
        );

        $days = JournalHelper::getDaysInMonth(
            journal: $journal,
            year: $journalEntry->year,
            month: $journalEntry->month,
            day: $journalEntry->day,
        );

        $payload = new JournalEntryPresenter($journalEntry)->build();

        return view('app.journal.entry.edit', [
            'journal' => $journal,
            'entry' => $journalEntry,
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'columns' => $payload['columns'],
            'notes' => $payload['notes'],
            'layoutColumnsCount' => $payload['layout_columns_count'],
        ]);
    }
}
