<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleSocialDensity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleSocialDensity>
 */
final class ModuleSocialDensityFactory extends Factory
{
    protected $model = ModuleSocialDensity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'social_density' => $this->faker->randomElement(['alone', 'few people', 'crowd', 'too much']),
        ];
    }
}
