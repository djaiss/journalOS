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
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->text('has_traveled_today')->nullable();
            $table->text('travel_details')->nullable()->after('has_traveled_today');
            $table->text('travel_mode')->nullable()->after('travel_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('travel_mode');
            $table->dropColumn('travel_details');
            $table->dropColumn('has_traveled_today');
        });
    }
};
