<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ModuleMeal;
use Illuminate\Database\Seeder;

final class ModuleMealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModuleMeal::factory()->count(5)->create();
    }
}
