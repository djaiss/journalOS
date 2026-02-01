<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleWeather;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModuleWeather>
 */
final class ModuleWeatherFactory extends Factory
{
    protected $model = ModuleWeather::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'condition' => $this->faker->randomElement(ModuleWeather::CONDITIONS),
            'temperature_range' => $this->faker->randomElement(ModuleWeather::TEMPERATURE_RANGES),
            'precipitation' => $this->faker->randomElement(ModuleWeather::PRECIPITATION_LEVELS),
            'daylight' => $this->faker->randomElement(ModuleWeather::DAYLIGHT_VALUES),
        ];
    }
}
