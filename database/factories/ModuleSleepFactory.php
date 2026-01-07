<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleSleep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleSleep>
 */
final class ModuleSleepFactory extends Factory
{
    protected $model = ModuleSleep::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'bedtime' => $this->faker->time('H:i'),
            'wake_up_time' => $this->faker->time('H:i'),
            'sleep_duration_in_minutes' => (string) $this->faker->numberBetween(240, 600),
        ];
    }
}
