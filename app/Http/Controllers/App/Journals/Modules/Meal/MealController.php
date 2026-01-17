<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Meal;

use App\Actions\LogMeal;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModuleMeal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class MealController extends Controller
{
    public function update(Request $request): RedirectResponse
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

        new LogMeal(
            user: Auth::user(),
            entry: $entry,
            breakfast: array_key_exists('breakfast', $validated) ? TextSanitizer::plainText($validated['breakfast']) : null,
            lunch: array_key_exists('lunch', $validated) ? TextSanitizer::plainText($validated['lunch']) : null,
            dinner: array_key_exists('dinner', $validated) ? TextSanitizer::plainText($validated['dinner']) : null,
            snack: array_key_exists('snack', $validated) ? TextSanitizer::plainText($validated['snack']) : null,
            mealType: array_key_exists('meal_type', $validated) ? TextSanitizer::plainText($validated['meal_type']) : null,
            socialContext: array_key_exists('social_context', $validated) ? TextSanitizer::plainText($validated['social_context']) : null,
            notes: array_key_exists('notes', $validated) ? TextSanitizer::nullablePlainText($validated['notes']) : null,
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
