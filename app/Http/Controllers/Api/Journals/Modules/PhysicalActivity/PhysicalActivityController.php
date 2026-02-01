<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Journals\Modules\PhysicalActivity;

use App\Actions\LogPhysicalActivity;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModulePhysicalActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class PhysicalActivityController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'has_done_physical_activity' => ['nullable', 'string', 'max:255', 'in:yes,no'],
            'activity_type' => ['nullable', 'string', 'max:255', Rule::in(ModulePhysicalActivity::ACTIVITY_TYPES)],
            'activity_intensity' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModulePhysicalActivity::ACTIVITY_INTENSITIES),
            ],
        ]);

        $entry = new LogPhysicalActivity(
            user: Auth::user(),
            entry: $entry,
            hasDonePhysicalActivity: array_key_exists('has_done_physical_activity', $validated)
                ? TextSanitizer::nullablePlainText($validated['has_done_physical_activity'])
                : null,
            activityType: array_key_exists('activity_type', $validated)
                ? TextSanitizer::nullablePlainText($validated['activity_type'])
                : null,
            activityIntensity: array_key_exists('activity_intensity', $validated)
                ? TextSanitizer::nullablePlainText($validated['activity_intensity'])
                : null,
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
