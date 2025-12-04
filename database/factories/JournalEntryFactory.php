<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JournalEntry>
 */
final class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'journal_id' => Journal::factory(),
            'day' => fake()->numberBetween(1, 31),
            'month' => fake()->numberBetween(1, 12),
            'year' => fake()->year(),
        ];
    }
}
