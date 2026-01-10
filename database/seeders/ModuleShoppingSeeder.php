<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ModuleShopping;
use Illuminate\Database\Seeder;

final class ModuleShoppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModuleShopping::factory()->count(5)->create();
    }
}
