<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\PhysicalActivity;

use App\Actions\LogPhysicalActivity;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class PhysicalActivityController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'has_done_physical_activity' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:activity_type,activity_intensity'],
            'activity_type' => ['nullable', 'string', 'max:255', 'in:running,cycling,swimming,gym,walking', 'required_without_all:has_done_physical_activity,activity_intensity'],
            'activity_intensity' => ['nullable', 'string', 'max:255', 'in:light,moderate,intense', 'required_without_all:has_done_physical_activity,activity_type'],
        ]);

        new LogPhysicalActivity(
            user: Auth::user(),
            entry: $entry,
            hasDonePhysicalActivity: array_key_exists('has_done_physical_activity', $validated)
                ? TextSanitizer::plainText($validated['has_done_physical_activity'])
                : null,
            activityType: array_key_exists('activity_type', $validated)
                ? TextSanitizer::plainText($validated['activity_type'])
                : null,
            activityIntensity: array_key_exists('activity_intensity', $validated)
                ? TextSanitizer::plainText($validated['activity_intensity'])
                : null,
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
