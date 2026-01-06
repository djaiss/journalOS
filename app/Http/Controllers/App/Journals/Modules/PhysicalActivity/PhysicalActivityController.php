<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\PhysicalActivity;

use App\Actions\LogHasDonePhysicalActivity;
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
            'has_done_physical_activity' => ['required', 'string', 'max:255', 'in:yes,no'],
        ]);

        new LogHasDonePhysicalActivity(
            user: Auth::user(),
            entry: $entry,
            hasDonePhysicalActivity: TextSanitizer::plainText($validated['has_done_physical_activity']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
