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
            $table->text('has_done_physical_activity')->nullable();
            $table->text('activity_type')->nullable()->after('has_done_physical_activity');
            $table->text('activity_intensity')->nullable()->after('activity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('activity_intensity');
            $table->dropColumn('activity_type');
            $table->dropColumn('has_done_physical_activity');
        });
    }
};
