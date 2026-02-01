<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JournalLlmAccessLog>
 */
final class JournalLlmAccessLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_id' => Journal::factory(),
            'requested_year' => 2026,
            'requested_month' => 1,
            'requested_day' => 27,
            'request_url' => $this->faker->url(),
        ];
    }
}
