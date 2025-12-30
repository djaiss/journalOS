<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Actions\ToggleModuleVisibility;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

final class JournalModulesController extends Controller
{
    public function update(Request $request): View
    {
        $journal = $request->attributes->get('journal');

        $validated = $request->validate([
            'module' => ['required', 'in:sleep'],
        ]);

        new ToggleModuleVisibility(
            user: Auth::user(),
            journal: $journal,
            moduleName: $validated['module'],
        )->execute();

        return view('app.journal.settings.show', [
            'journal' => $journal,
        ])->with('status', __('Changes saved'));
    }
}
