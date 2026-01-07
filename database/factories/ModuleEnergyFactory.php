<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleEnergy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleEnergy>
 */
final class ModuleEnergyFactory extends Factory
{
    protected $model = ModuleEnergy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'energy' => $this->faker->randomElement(['very low', 'low', 'normal', 'high', 'very high']),
        ];
    }
}
