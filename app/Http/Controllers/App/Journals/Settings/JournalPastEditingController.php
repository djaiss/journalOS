<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Actions\ToggleJournalPastEditing;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class JournalPastEditingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $validated = $request->validate([
            'can_edit_past' => ['required', 'boolean'],
        ]);

        $newValue = (bool) $validated['can_edit_past'];

        // only toggle if the value is different from current
        if ($journal->can_edit_past !== $newValue) {
            $action = new ToggleJournalPastEditing(
                user: Auth::user(),
                journal: $journal,
            );
            $journal = $action->execute();
        }

        return to_route('journal.settings.management.index', [
            'slug' => $journal->slug,
        ])->with('status', __('Changes saved'));
    }
}
