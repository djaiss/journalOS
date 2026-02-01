<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleReading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleReading>
 */
final class ModuleReadingFactory extends Factory
{
    protected $model = ModuleReading::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'did_read_today' => 'yes',
            'reading_amount' => $this->faker->randomElement(ModuleReading::READING_AMOUNTS),
            'mental_state' => $this->faker->randomElement(ModuleReading::MENTAL_STATES),
            'reading_feel' => $this->faker->randomElement(ModuleReading::READING_FEELS),
            'want_continue' => $this->faker->randomElement(ModuleReading::WANT_CONTINUE_OPTIONS),
            'reading_limit' => $this->faker->randomElement(ModuleReading::READING_LIMITS),
        ];
    }
}
