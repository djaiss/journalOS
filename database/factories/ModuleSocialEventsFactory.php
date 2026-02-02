<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleSocialEvents;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleSocialEvents>
 */
final class ModuleSocialEventsFactory extends Factory
{
    protected $model = ModuleSocialEvents::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'event_type' => $this->faker->randomElement(ModuleSocialEvents::EVENT_TYPE_VALUES),
            'tone' => $this->faker->randomElement(ModuleSocialEvents::TONE_VALUES),
            'duration' => $this->faker->randomElement(ModuleSocialEvents::DURATION_VALUES),
        ];
    }
}
