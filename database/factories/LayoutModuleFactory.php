<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Layout;
use App\Models\LayoutModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LayoutModule>
 */
final class LayoutModuleFactory extends Factory
{
    protected $model = LayoutModule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layout_id' => Layout::factory(),
            'module_key' => $this->faker->randomElement(LayoutModule::allowedModuleKeys()),
            'column_number' => $this->faker->numberBetween(1, 4),
            'position' => $this->faker->numberBetween(1, 5),
        ];
    }
}
