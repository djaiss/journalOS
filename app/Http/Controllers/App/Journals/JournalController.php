<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals;

use App\Actions\CreateJournal;
use App\Actions\DestroyJournal;
use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class JournalController extends Controller
{
    public function index(): View
    {
        $journals = Journal::query()
            ->where('user_id', Auth::user()->id)
            ->get();

        return view('app.journal.index', [
            'journals' => $journals,
        ]);
    }

    public function create(): View
    {
        return view('app.journal.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'journal_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],
        ]);

        $journal = new CreateJournal(
            user: Auth::user(),
            name: $validated['journal_name'],
        )->execute();

        return to_route('journal.show', $journal->slug)
            ->with('status', __('Journal created successfully'));
    }

    public function show(Request $request): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $day = (int) now()->format('d');
        $month = (int) now()->format('m');
        $year = (int) now()->format('Y');

        return to_route('journal.entry.show', [
            'slug' => $journal->slug,
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        new DestroyJournal(
            user: Auth::user(),
            journal: $journal,
        )->execute();

        return to_route('journal.index')
            ->with('status', trans('The journal has been deleted'));
    }
}
