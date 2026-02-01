<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleDayType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleDayType>
 */
final class ModuleDayTypeFactory extends Factory
{
    protected $model = ModuleDayType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'day_type' => $this->faker->randomElement([
                'workday',
                'day off',
                'weekend',
                'vacation',
                'sick day',
            ]),
        ];
    }
}
