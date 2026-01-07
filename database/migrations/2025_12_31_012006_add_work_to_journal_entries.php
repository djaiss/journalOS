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
            $table->text('worked')->nullable();
            $table->text('work_mode')->nullable()->after('worked');
            $table->text('work_load')->nullable()->after('work_mode');
            $table->text('work_procrastinated')->nullable()->after('work_load');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('work_procrastinated');
            $table->dropColumn('work_load');
            $table->dropColumn('work_mode');
            $table->dropColumn('worked');
        });
    }
};
