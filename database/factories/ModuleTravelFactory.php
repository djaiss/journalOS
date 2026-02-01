<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleTravel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleTravel>
 */
final class ModuleTravelFactory extends Factory
{
    protected $model = ModuleTravel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $travelModes = ['car', 'plane', 'train', 'bike', 'bus', 'walk', 'boat', 'other'];

        return [
            'journal_entry_id' => JournalEntry::factory(),
            'has_traveled_today' => $this->faker->randomElement(['yes', 'no']),
            'travel_details' => $this->faker->sentence(),
            'travel_mode' => array_values($this->faker->randomElements(
                $travelModes,
                $this->faker->numberBetween(1, 3),
            )),
        ];
    }
}
