<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleMood;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleMood>
 */
final class ModuleMoodFactory extends Factory
{
    protected $model = ModuleMood::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'mood' => $this->faker->randomElement(['terrible', 'bad', 'okay', 'good', 'great']),
        ];
    }
}
