<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleSexualActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleSexualActivity>
 */
final class ModuleSexualActivityFactory extends Factory
{
    protected $model = ModuleSexualActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'had_sexual_activity' => $this->faker->randomElement(['yes', 'no']),
            'sexual_activity_type' => $this->faker->randomElement(['solo', 'with-partner', 'intimate-contact']),
        ];
    }
}
