<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Actions\RenameJournal;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class JournalSettingsController extends Controller
{
    public function show(Request $request): View
    {
        $journal = $request->attributes->get('journal');

        return view('app.journal.settings.show', [
            'journal' => $journal,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $validated = $request->validate([
            'journal_name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        new RenameJournal(
            user: Auth::user(),
            journal: $journal,
            name: $validated['journal_name'],
        )->execute();

        return to_route('journal.settings.show', [
            'slug' => $journal->slug,
        ])->with('status', trans('API key created'));
    }
}
