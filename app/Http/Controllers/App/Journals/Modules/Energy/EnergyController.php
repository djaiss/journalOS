<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Energy;

use App\Actions\LogEnergy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class EnergyController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'energy' => ['required', 'string', 'max:255', 'in:very low,low,normal,high,very high'],
        ]);

        new LogEnergy(
            user: Auth::user(),
            entry: $entry,
            energy: $validated['energy'],
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
