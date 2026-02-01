<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\ModuleWeatherInfluence;
use Illuminate\Database\Seeder;

final class ModuleWeatherInfluenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModuleWeatherInfluence::factory()->count(5)->create();
    }
}
