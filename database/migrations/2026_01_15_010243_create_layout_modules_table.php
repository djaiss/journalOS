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
        Schema::create('layout_modules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('layout_id');
            $table->string('module_key');
            $table->unsignedTinyInteger('column_number');
            $table->unsignedSmallInteger('position');
            $table->unique(['layout_id', 'module_key']);
            $table->timestamps();
            $table->foreign('layout_id')->references('id')->on('layouts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layout_modules');
    }
};
