<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\ModuleCognitiveLoad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleCognitiveLoad>
 */
final class ModuleCognitiveLoadFactory extends Factory
{
    protected $model = ModuleCognitiveLoad::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'cognitive_load' => $this->faker->randomElement(ModuleCognitiveLoad::COGNITIVE_LOAD_LEVELS),
            'primary_source' => $this->faker->randomElement(ModuleCognitiveLoad::PRIMARY_SOURCES),
            'load_quality' => $this->faker->randomElement(ModuleCognitiveLoad::LOAD_QUALITIES),
        ];
    }
}
