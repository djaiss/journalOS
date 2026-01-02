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
            $table->text('bedtime')->nullable()->after('has_content');
            $table->text('wake_up_time')->nullable()->after('bedtime');
            $table->text('sleep_duration_in_minutes')->nullable()->after('wake_up_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('sleep_duration_in_minutes');
            $table->dropColumn('wake_up_time');
            $table->dropColumn('bedtime');
        });
    }
};
