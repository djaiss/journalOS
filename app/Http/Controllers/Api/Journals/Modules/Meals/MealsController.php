<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Meals;

use App\Actions\LogMeals;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleMeals;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class MealsController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'meal_presence' => ['nullable', 'array', 'min:1', 'required_without_all:meal_type,social_context,has_notes,notes'],
            'meal_presence.*' => [
                'string',
                'max:255',
                Rule::in(ModuleMeals::MEAL_PRESENCE),
            ],
            'meal_type' => ['nullable', 'string', 'max:255', Rule::in(ModuleMeals::MEAL_TYPES), 'required_without_all:meal_presence,social_context,has_notes,notes'],
            'social_context' => ['nullable', 'string', 'max:255', Rule::in(ModuleMeals::SOCIAL_CONTEXTS), 'required_without_all:meal_presence,meal_type,has_notes,notes'],
            'has_notes' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:meal_presence,meal_type,social_context,notes'],
            'notes' => ['nullable', 'string', 'max:1000', 'required_without_all:meal_presence,meal_type,social_context,has_notes'],
        ]);

        $entry = new LogMeals(
            user: Auth::user(),
            entry: $entry,
            mealPresence: array_key_exists('meal_presence', $validated)
                ? array_map(TextSanitizer::plainText(...), $validated['meal_presence'])
                : null,
            mealType: array_key_exists('meal_type', $validated) ? TextSanitizer::nullablePlainText($validated['meal_type']) : null,
            socialContext: array_key_exists('social_context', $validated) ? TextSanitizer::nullablePlainText($validated['social_context']) : null,
            hasNotes: array_key_exists('has_notes', $validated) ? TextSanitizer::nullablePlainText($validated['has_notes']) : null,
            notes: array_key_exists('notes', $validated) ? TextSanitizer::nullablePlainText($validated['notes']) : null,
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
