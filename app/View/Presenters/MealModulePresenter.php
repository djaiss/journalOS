<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleMeal;

final readonly class MealModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $moduleMeal = $this->entry->moduleMeal;

        return [
            'breakfast' => $moduleMeal?->breakfast,
            'lunch' => $moduleMeal?->lunch,
            'dinner' => $moduleMeal?->dinner,
            'snack' => $moduleMeal?->snack,
            'meal_type' => $moduleMeal?->meal_type,
            'meal_type_options' => $this->mealTypeOptions(),
            'social_context' => $moduleMeal?->social_context,
            'social_context_options' => $this->socialContextOptions(),
            'notes' => $moduleMeal?->notes,
            'meal_url' => route('journal.entry.meal.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.meal.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'display_reset' => $this->displayReset(),
        ];
    }

    private function mealTypeOptions(): array
    {
        $mealType = $this->entry->moduleMeal?->meal_type;

        return collect(ModuleMeal::MEAL_TYPES)->map(fn(string $value) => [
            'value' => $value,
            'label' => match ($value) {
                'home_cooked' => __('Home-cooked'),
                'takeout' => __('Takeout'),
                'restaurant' => __('Restaurant'),
                'work_cafeteria' => __('Work cafeteria'),
                default => $value,
            },
            'is_selected' => $mealType === $value,
        ])->all();
    }

    private function socialContextOptions(): array
    {
        $socialContext = $this->entry->moduleMeal?->social_context;

        return collect(ModuleMeal::SOCIAL_CONTEXTS)->map(fn(string $value) => [
            'value' => $value,
            'label' => match ($value) {
                'alone' => __('Alone'),
                'family' => __('Family'),
                'friends' => __('Friends'),
                'colleagues' => __('Colleagues'),
                default => $value,
            },
            'is_selected' => $socialContext === $value,
        ])->all();
    }

    private function displayReset(): bool
    {
        $moduleMeal = $this->entry->moduleMeal;

        if ($moduleMeal === null) {
            return false;
        }

        return ! is_null($moduleMeal->breakfast)
            || ! is_null($moduleMeal->lunch)
            || ! is_null($moduleMeal->dinner)
            || ! is_null($moduleMeal->snack)
            || ! is_null($moduleMeal->meal_type)
            || ! is_null($moduleMeal->social_context)
            || ! is_null($moduleMeal->notes);
    }
}
