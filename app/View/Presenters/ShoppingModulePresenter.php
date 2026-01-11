<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleShopping;

final readonly class ShoppingModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $moduleShopping = $this->entry->moduleShopping;

        return [
            'has_shopped_today' => $moduleShopping?->has_shopped_today,
            'shopping_url' => route('journal.entry.shopping.update', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'shopping_type' => $moduleShopping?->shopping_type,
            'shopping_types' => $this->shoppingTypes(),
            'shopping_intent' => $moduleShopping?->shopping_intent,
            'shopping_intents' => $this->shoppingIntents(),
            'shopping_context' => $moduleShopping?->shopping_context,
            'shopping_contexts' => $this->shoppingContexts(),
            'shopping_for' => $moduleShopping?->shopping_for,
            'shopping_for_options' => $this->shoppingForOptions(),
            'reset_url' => route('journal.entry.shopping.reset', [
                'slug' => $this->entry->journal->slug,
                'year' => $this->entry->year,
                'month' => $this->entry->month,
                'day' => $this->entry->day,
            ]),
            'display_reset' => $this->displayReset(),
        ];
    }

    private function shoppingTypes(): array
    {
        $selectedTypes = $this->entry->moduleShopping?->shopping_type;

        return collect(ModuleShopping::SHOPPING_TYPES)->map(fn(string $type) => [
            'value' => $type,
            'label' => match ($type) {
                'groceries' => __('Groceries'),
                'clothes' => __('Clothes'),
                'electronics_tech' => __('Electronics / tech'),
                'household_essentials' => __('Household / essentials'),
                'books_media' => __('Books / media'),
                'gifts' => __('Gifts'),
                'online_shopping' => __('Online shopping'),
                'other' => __('Other'),
                default => $type,
            },
            'is_selected' => is_array($selectedTypes) && in_array($type, $selectedTypes, true),
        ])->all();
    }

    private function shoppingIntents(): array
    {
        $intent = $this->entry->moduleShopping?->shopping_intent;

        return collect(ModuleShopping::SHOPPING_INTENTS)->map(fn(string $value) => [
            'value' => $value,
            'label' => match ($value) {
                'planned' => __('Planned'),
                'opportunistic' => __('Opportunistic'),
                'impulse' => __('Impulse'),
                'replacement' => __('Replacement'),
                default => $value,
            },
            'is_selected' => $intent === $value,
        ])->all();
    }

    private function shoppingContexts(): array
    {
        $context = $this->entry->moduleShopping?->shopping_context;

        return collect(ModuleShopping::SHOPPING_CONTEXTS)->map(fn(string $value) => [
            'value' => $value,
            'label' => match ($value) {
                'alone' => __('Alone'),
                'with_partner' => __('With partner'),
                'with_kids' => __('With kids'),
                default => $value,
            },
            'is_selected' => $context === $value,
        ])->all();
    }

    private function shoppingForOptions(): array
    {
        $shoppingFor = $this->entry->moduleShopping?->shopping_for;

        return collect(ModuleShopping::SHOPPING_FOR_OPTIONS)->map(fn(string $value) => [
            'value' => $value,
            'label' => match ($value) {
                'for_self' => __('For self'),
                'for_household' => __('For household'),
                'for_others' => __('For others'),
                default => $value,
            },
            'is_selected' => $shoppingFor === $value,
        ])->all();
    }

    private function displayReset(): bool
    {
        $moduleShopping = $this->entry->moduleShopping;

        if ($moduleShopping === null) {
            return false;
        }

        return ! is_null($moduleShopping->has_shopped_today)
            || ! is_null($moduleShopping->shopping_type)
            || ! is_null($moduleShopping->shopping_intent)
            || ! is_null($moduleShopping->shopping_context)
            || ! is_null($moduleShopping->shopping_for);
    }
}
