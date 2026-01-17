<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Meal;

use App\Actions\LogMeal;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleMeal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class MealController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'breakfast' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:lunch,dinner,snack,meal_type,social_context,notes'],
            'lunch' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:breakfast,dinner,snack,meal_type,social_context,notes'],
            'dinner' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:breakfast,lunch,snack,meal_type,social_context,notes'],
            'snack' => ['nullable', 'string', 'max:255', 'in:yes,no', 'required_without_all:breakfast,lunch,dinner,meal_type,social_context,notes'],
            'meal_type' => ['nullable', 'string', 'max:255', Rule::in(ModuleMeal::MEAL_TYPES), 'required_without_all:breakfast,lunch,dinner,snack,social_context,notes'],
            'social_context' => ['nullable', 'string', 'max:255', Rule::in(ModuleMeal::SOCIAL_CONTEXTS), 'required_without_all:breakfast,lunch,dinner,snack,meal_type,notes'],
            'notes' => ['nullable', 'string', 'min:1', 'max:1000', 'required_without_all:breakfast,lunch,dinner,snack,meal_type,social_context'],
        ]);

        $entry = new LogMeal(
            user: Auth::user(),
            entry: $entry,
            breakfast: array_key_exists('breakfast', $validated) ? TextSanitizer::nullablePlainText($validated['breakfast']) : null,
            lunch: array_key_exists('lunch', $validated) ? TextSanitizer::nullablePlainText($validated['lunch']) : null,
            dinner: array_key_exists('dinner', $validated) ? TextSanitizer::nullablePlainText($validated['dinner']) : null,
            snack: array_key_exists('snack', $validated) ? TextSanitizer::nullablePlainText($validated['snack']) : null,
            mealType: array_key_exists('meal_type', $validated) ? TextSanitizer::nullablePlainText($validated['meal_type']) : null,
            socialContext: array_key_exists('social_context', $validated) ? TextSanitizer::nullablePlainText($validated['social_context']) : null,
            notes: array_key_exists('notes', $validated) ? TextSanitizer::nullablePlainText($validated['notes']) : null,
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
