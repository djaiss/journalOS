<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
final class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'slug' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Organization $organization): void {
            $organization->slug = $organization->id . '-' . Str::lower($organization->name);
            $organization->save();
        });
    }
}
