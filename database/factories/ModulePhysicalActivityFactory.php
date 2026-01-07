<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModulePhysicalActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModulePhysicalActivity>
 */
final class ModulePhysicalActivityFactory extends Factory
{
    protected $model = ModulePhysicalActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'has_done_physical_activity' => $this->faker->randomElement(['yes', 'no']),
            'activity_type' => $this->faker->randomElement(['running', 'cycling', 'swimming', 'gym', 'walking']),
            'activity_intensity' => $this->faker->randomElement(['light', 'moderate', 'intense']),
        ];
    }
}
