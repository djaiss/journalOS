<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Actions\ToggleLLMForJournal;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class JournalLLMSettingsController extends Controller
{
    public function show(Request $request): View
    {
        $journal = $request->attributes->get('journal');
        $accessLogs = $journal
            ->llmAccessLogs()
            ->latest()
            ->take(11)
            ->get();

        return view('app.journal.settings.llm.index', [
            'journal' => $journal,
            'accessLogs' => $accessLogs->take(10)->values(),
            'hasMoreAccessLogs' => $accessLogs->count() > 10,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $validated = $request->validate([
            'has_llm_access' => ['required', 'boolean'],
        ]);

        $newValue = (bool) $validated['has_llm_access'];

        if ($journal->has_llm_access !== $newValue) {
            $action = new ToggleLLMForJournal(
                user: Auth::user(),
                journal: $journal,
            );
            $journal = $action->execute();
        }

        return to_route('journal.settings.llm.index', [
            'slug' => $journal->slug,
        ])->with('status', __('Changes saved'));
    }
}
