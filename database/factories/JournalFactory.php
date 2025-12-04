<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Journal>
 */
final class JournalFactory extends Factory
{
    protected $model = Journal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'slug' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Journal $journal): void {
            $journal->slug = $journal->id . '-' . Str::lower($journal->name);
            $journal->save();
        });
    }
}
