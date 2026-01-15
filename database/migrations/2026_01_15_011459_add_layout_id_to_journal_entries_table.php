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
            $table->unsignedBigInteger('layout_id')->nullable();
            $table->index('layout_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropIndex(['layout_id']);
            $table->dropColumn('layout_id');
        });
    }
};
