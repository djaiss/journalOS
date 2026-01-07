<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleHealth;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleHealth>
 */
final class ModuleHealthFactory extends Factory
{
    protected $model = ModuleHealth::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'health' => $this->faker->randomElement(['good', 'okay', 'not great']),
        ];
    }
}
