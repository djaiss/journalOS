<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals\Modules\Energy;

use App\Actions\LogEnergy;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModuleEnergy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class EnergyController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'energy' => ['required', 'string', 'max:255', Rule::in(ModuleEnergy::ENERGY_LEVELS)],
        ]);

        new LogEnergy(
            user: Auth::user(),
            entry: $entry,
            energy: TextSanitizer::plainText($validated['energy']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
