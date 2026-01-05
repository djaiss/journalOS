<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Actions\ToggleModuleVisibility;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class JournalModulesController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $validated = $request->validate([
            'module' => ['required', 'string', 'max:255'],
        ]);

        $journal = new ToggleModuleVisibility(
            user: Auth::user(),
            journal: $journal,
            moduleName: $validated['module'],
        )->execute();

        return to_route('journal.settings.show', [
            'slug' => $journal->slug,
        ])->with('status', __('Changes saved'));
    }
}
