<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleMeals;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleMeals>
 */
final class ModuleMealsFactory extends Factory
{
    protected $model = ModuleMeals::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'meal_presence' => array_values($this->faker->randomElements(
                ModuleMeals::MEAL_PRESENCE,
                $this->faker->numberBetween(1, 3),
            )),
            'meal_type' => $this->faker->randomElement(ModuleMeals::MEAL_TYPES),
            'social_context' => $this->faker->randomElement(ModuleMeals::SOCIAL_CONTEXTS),
            'has_notes' => $this->faker->randomElement(['yes', 'no']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
