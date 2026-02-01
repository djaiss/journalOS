<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleMeals;

final readonly class MealsModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $moduleMeals = $this->entry->moduleMeals;

        return [
            'meal_presence' => $moduleMeals?->meal_presence,
            'meal_presence_options' => $this->mealPresenceOptions(),
            'meal_type' => $moduleMeals?->meal_type,
            'meal_type_options' => $this->mealTypeOptions(),
            'social_context' => $moduleMeals?->social_context,
            'social_context_options' => $this->socialContextOptions(),
            'has_notes' => $moduleMeals?->has_notes,
            'notes' => $moduleMeals?->notes,
            'meals_url' => route('journal.entry.meals.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'reset_url' => route('journal.entry.meals.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'display_reset' => $this->displayReset(),
        ];
    }

    private function mealPresenceOptions(): array
    {
        $selectedPresence = $this->entry->moduleMeals?->meal_presence;

        return collect(ModuleMeals::MEAL_PRESENCE)->map(fn (string $value) => [
            'value' => $value,
            'label' => match ($value) {
                'breakfast' => __('Breakfast'),
                'lunch' => __('Lunch'),
                'dinner' => __('Dinner'),
                'snack' => __('Snack'),
                default => $value,
            },
            'is_selected' => is_array($selectedPresence) && in_array($value, $selectedPresence, true),
        ])->all();
    }

    private function mealTypeOptions(): array
    {
        $mealType = $this->entry->moduleMeals?->meal_type;

        return collect(ModuleMeals::MEAL_TYPES)->map(fn (string $value) => [
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
        $socialContext = $this->entry->moduleMeals?->social_context;

        return collect(ModuleMeals::SOCIAL_CONTEXTS)->map(fn (string $value) => [
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
        $moduleMeals = $this->entry->moduleMeals;

        if ($moduleMeals === null) {
            return false;
        }

        return (
            !is_null($moduleMeals->meal_presence)
            || !is_null($moduleMeals->meal_type)
            || !is_null($moduleMeals->social_context)
            || !is_null($moduleMeals->has_notes)
            || !is_null($moduleMeals->notes)
        );
    }
}
