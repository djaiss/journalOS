<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Actions\SetActiveLayout;
use App\Http\Controllers\Controller;
use App\Models\Layout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class JournalLayoutDefaultController extends Controller
{
    public function update(Request $request, string $slug, int $layout): RedirectResponse
    {
        $journal = $request->attributes->get('journal');

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->findOrFail($layout);

        (new SetActiveLayout(
            user: Auth::user(),
            layout: $layout,
        ))->execute();

        return to_route('journal.settings.modules.index', [
            'slug' => $journal->slug,
        ])->with('status', __('Layout set as default'));
    }
}
