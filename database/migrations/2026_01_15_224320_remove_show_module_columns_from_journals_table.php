<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn([
                'show_sleep_module',
                'show_work_module',
                'show_travel_module',
                'show_kids_module',
                'show_day_type_module',
                'show_primary_obligation_module',
                'show_physical_activity_module',
                'show_health_module',
                'show_mood_module',
                'show_energy_module',
                'show_sexual_activity_module',
                'show_social_density_module',
                'show_shopping_module',
                'show_hygiene_module',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->boolean('show_sleep_module')->default(true);
            $table->boolean('show_work_module')->default(true);
            $table->boolean('show_travel_module')->default(true);
            $table->boolean('show_kids_module')->default(false);
            $table->boolean('show_day_type_module')->default(true);
            $table->boolean('show_primary_obligation_module')->default(true);
            $table->boolean('show_physical_activity_module')->default(true);
            $table->boolean('show_health_module')->default(true);
            $table->boolean('show_mood_module')->default(true);
            $table->boolean('show_energy_module')->default(true);
            $table->boolean('show_sexual_activity_module')->default(false);
            $table->boolean('show_social_density_module')->default(true);
            $table->boolean('show_shopping_module')->default(true);
            $table->boolean('show_hygiene_module')->default(true);
        });
    }
};
