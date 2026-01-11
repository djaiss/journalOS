<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\PrimaryObligation;

use App\Actions\LogPrimaryObligation;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModulePrimaryObligation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class PrimaryObligationController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'primary_obligation' => ['required', 'string', 'max:255', Rule::in(ModulePrimaryObligation::PRIMARY_OBLIGATIONS)],
        ]);

        new LogPrimaryObligation(
            user: Auth::user(),
            entry: $entry,
            primaryObligation: TextSanitizer::plainText($validated['primary_obligation']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
