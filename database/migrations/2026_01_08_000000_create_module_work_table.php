<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('module_work', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->text('worked')->nullable();
            $table->text('work_mode')->nullable();
            $table->text('work_load')->nullable();
            $table->text('work_procrastinated')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_work');
    }
};
