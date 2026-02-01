<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleShopping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleShopping>
 */
final class ModuleShoppingFactory extends Factory
{
    protected $model = ModuleShopping::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shoppingTypes = [
            'groceries',
            'clothes',
            'electronics_tech',
            'household_essentials',
            'books_media',
            'gifts',
            'online_shopping',
            'other',
        ];

        return [
            'journal_entry_id' => JournalEntry::factory(),
            'has_shopped_today' => $this->faker->randomElement(['yes', 'no']),
            'shopping_type' => array_values($this->faker->randomElements(
                $shoppingTypes,
                $this->faker->numberBetween(1, 3),
            )),
            'shopping_intent' => $this->faker->randomElement(['planned', 'opportunistic', 'impulse', 'replacement']),
            'shopping_context' => $this->faker->randomElement(['alone', 'with_partner', 'with_kids']),
            'shopping_for' => $this->faker->randomElement(['for_self', 'for_household', 'for_others']),
        ];
    }
}
