<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModulePrimaryObligation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModulePrimaryObligation>
 */
final class ModulePrimaryObligationFactory extends Factory
{
    protected $model = ModulePrimaryObligation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'primary_obligation' => $this->faker->randomElement(['work', 'family', 'personal', 'health', 'travel', 'none']),
        ];
    }
}
