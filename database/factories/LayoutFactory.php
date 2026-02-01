<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Journal;
use App\Models\Layout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Layout>
 */
final class LayoutFactory extends Factory
{
    protected $model = Layout::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_id' => Journal::factory(),
            'name' => $this->faker->word(),
            'columns_count' => $this->faker->numberBetween(1, 4),
            'is_active' => false,
        ];
    }
}
