<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Actions\AddModuleToLayout;
use App\Actions\RemoveModuleFromLayout;
use App\Actions\ReorderLayoutModule;
use App\Http\Controllers\Controller;
use App\Models\Layout;
use App\View\Presenters\LayoutModulesPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class JournalLayoutModulesController extends Controller
{
    public function show(Request $request, string $slug, int $layout): View
    {
        $journal = $request->attributes->get('journal');

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->findOrFail($layout);

        $presenter = new LayoutModulesPresenter($layout);
        $payload = $presenter->build();

        return view('app.journal.settings.layouts.modules.index', [
            'journal' => $journal,
            'layout' => $layout,
            'columns' => $payload['columns'],
            'availableModules' => $payload['available_modules'],
        ]);
    }

    public function store(Request $request, string $slug, int $layout): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $validated = $request->validate([
            'module_key' => ['required', 'string', 'max:255'],
            'column_number' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->findOrFail($layout);

        new AddModuleToLayout(
            user: Auth::user(),
            layout: $layout,
            moduleKey: $validated['module_key'],
            columnNumber: (int) $validated['column_number'],
        )->execute();

        return to_route('journal.settings.layouts.modules.index', [
            'slug' => $journal->slug,
            'layout' => $layout->id,
        ])->with('status', __('Module added to layout'));
    }

    public function destroy(Request $request, string $slug, int $layout, string $moduleKey): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->findOrFail($layout);

        new RemoveModuleFromLayout(
            user: Auth::user(),
            layout: $layout,
            moduleKey: $moduleKey,
        )->execute();

        return to_route('journal.settings.layouts.modules.index', [
            'slug' => $journal->slug,
            'layout' => $layout->id,
        ])->with('status', __('Module removed from layout'));
    }

    public function reorder(Request $request, string $slug, int $layout): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $validated = $request->validate([
            'module_key' => ['required', 'string', 'max:255'],
            'column_number' => ['required', 'integer', 'min:1', 'max:4'],
            'position' => ['required', 'integer', 'min:1'],
        ]);

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->findOrFail($layout);

        new ReorderLayoutModule(
            user: Auth::user(),
            layout: $layout,
            moduleKey: $validated['module_key'],
            columnNumber: (int) $validated['column_number'],
            position: (int) $validated['position'],
        )->execute();

        return to_route('journal.settings.layouts.modules.index', [
            'slug' => $journal->slug,
            'layout' => $layout->id,
        ])->with('status', __('Layout module reordered'));
    }
}
