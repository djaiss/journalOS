<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table): void {
            $table->boolean('show_kids_module')->default(false)->after('show_travel_module');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table): void {
            $table->dropColumn('show_kids_module');
        });
    }
};
