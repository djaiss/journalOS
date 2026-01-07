<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleWork;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleWork>
 */
final class ModuleWorkFactory extends Factory
{
    protected $model = ModuleWork::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'worked' => $this->faker->randomElement(['yes', 'no']),
            'work_mode' => $this->faker->randomElement(['on-site', 'remote', 'hybrid']),
            'work_load' => $this->faker->randomElement(['light', 'medium', 'heavy']),
            'work_procrastinated' => $this->faker->randomElement(['yes', 'no']),
        ];
    }
}
