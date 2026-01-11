<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Health;

use App\Actions\LogHealth;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModuleHealth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;

final class HealthController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'health' => ['required', 'string', 'max:255', Rule::in(ModuleHealth::HEALTH_VALUES)],
        ]);

        new LogHealth(
            user: Auth::user(),
            entry: $entry,
            health: TextSanitizer::plainText($validated['health']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
