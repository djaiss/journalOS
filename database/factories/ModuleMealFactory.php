<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleMeal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleMeal>
 */
final class ModuleMealFactory extends Factory
{
    protected $model = ModuleMeal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'breakfast' => $this->faker->randomElement(ModuleMeal::MEAL_PRESENCE),
            'lunch' => $this->faker->randomElement(ModuleMeal::MEAL_PRESENCE),
            'dinner' => $this->faker->randomElement(ModuleMeal::MEAL_PRESENCE),
            'snack' => $this->faker->randomElement(ModuleMeal::MEAL_PRESENCE),
            'meal_type' => $this->faker->randomElement(ModuleMeal::MEAL_TYPES),
            'social_context' => $this->faker->randomElement(ModuleMeal::SOCIAL_CONTEXTS),
            'notes' => $this->faker->sentence(),
        ];
    }
}
