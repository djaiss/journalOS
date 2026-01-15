<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Actions\CreateLayout;
use App\Actions\DestroyLayout;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\Layout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class JournalLayoutsController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'columns_count' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        new CreateLayout(
            user: Auth::user(),
            journal: $journal,
            name: TextSanitizer::plainText($validated['name']),
            columnsCount: (int) $validated['columns_count'],
        )->execute();

        return to_route('journal.settings.modules.index', [
            'slug' => $journal->slug,
        ])->with('status', __('Layout created'));
    }

    public function destroy(Request $request, string $slug, int $layout): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->findOrFail($layout);

        (new DestroyLayout(
            user: Auth::user(),
            layout: $layout,
        ))->execute();

        return to_route('journal.settings.modules.index', [
            'slug' => $journal->slug,
        ])->with('status', __('Layout deleted'));
    }
}
