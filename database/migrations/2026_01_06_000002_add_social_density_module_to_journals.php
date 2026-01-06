<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table): void {
            $table->boolean('show_social_density_module')->default(true)->after('show_energy_module');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table): void {
            $table->dropColumn('show_social_density_module');
        });
    }
};
