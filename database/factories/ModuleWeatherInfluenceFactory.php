<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleWeatherInfluence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleWeatherInfluence>
 */
final class ModuleWeatherInfluenceFactory extends Factory
{
    protected $model = ModuleWeatherInfluence::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'mood_effect' => $this->faker->randomElement(ModuleWeatherInfluence::MOOD_EFFECTS),
            'energy_effect' => $this->faker->randomElement(ModuleWeatherInfluence::ENERGY_EFFECTS),
            'plans_influence' => $this->faker->randomElement(ModuleWeatherInfluence::PLANS_INFLUENCES),
            'outside_time' => $this->faker->randomElement(ModuleWeatherInfluence::OUTSIDE_TIMES),
        ];
    }
}
