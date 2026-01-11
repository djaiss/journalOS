<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleHygiene;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleHygiene>
 */
final class ModuleHygieneFactory extends Factory
{
    protected $model = ModuleHygiene::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'showered' => $this->faker->randomElement(['yes', 'no']),
            'brushed_teeth' => $this->faker->randomElement(['no', 'am', 'pm']),
            'skincare' => $this->faker->randomElement(['yes', 'no']),
        ];
    }
}
