<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\PhysicalActivity;

use App\Actions\LogActivityIntensity;
use App\Actions\LogActivityType;
use App\Actions\LogHasDonePhysicalActivity;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class PhysicalActivityController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'has_done_physical_activity' => ['nullable', 'string', 'in:yes,no'],
            'activity_type' => ['nullable', 'string', 'in:running,cycling,swimming,gym,walking'],
            'activity_intensity' => ['nullable', 'string', 'in:light,moderate,intense'],
        ]);

        // Log has_done_physical_activity if provided
        if (isset($validated['has_done_physical_activity'])) {
            $entry = new LogHasDonePhysicalActivity(
                user: Auth::user(),
                entry: $entry,
                hasDonePhysicalActivity: $validated['has_done_physical_activity'],
            )->execute();
        }

        // Log activity_type if provided
        if (isset($validated['activity_type'])) {
            $entry = new LogActivityType(
                user: Auth::user(),
                entry: $entry,
                activityType: $validated['activity_type'],
            )->execute();
        }

        // Log activity_intensity if provided
        if (isset($validated['activity_intensity'])) {
            $entry = new LogActivityIntensity(
                user: Auth::user(),
                entry: $entry,
                activityIntensity: $validated['activity_intensity'],
            )->execute();
        }

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
