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
        Schema::table('journals', function (Blueprint $table): void {
            $table->boolean('has_llm_access')->default(false)->after('can_edit_past');
            $table->string('llm_access_key')->nullable()->after('has_llm_access');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table): void {
            $table->dropColumn('llm_access_key');
            $table->dropColumn('has_llm_access');
        });
    }
};
