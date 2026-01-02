<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\PhysicalActivity;

use App\Actions\LogActivityIntensity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class PhysicalActivityIntensityController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'activity_intensity' => ['required', 'string', 'in:light,moderate,intense'],
        ]);

        new LogActivityIntensity(
            user: Auth::user(),
            entry: $entry,
            activityIntensity: $validated['activity_intensity'],
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
