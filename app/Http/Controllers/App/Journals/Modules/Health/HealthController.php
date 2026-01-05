<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Health;

use App\Actions\LogHealth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class HealthController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'health' => ['required', 'string', 'max:255', 'in:good,okay,not great'],
        ]);

        new LogHealth(
            user: Auth::user(),
            entry: $entry,
            health: $validated['health'],
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
