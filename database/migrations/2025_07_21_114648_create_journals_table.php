<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('name');
            $table->text('slug')->nullable();
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
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
