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
        Schema::create('book_journal_entry', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->index('book_id');
            $table->unsignedBigInteger('journal_entry_id');
            $table->index('journal_entry_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_journal_entry');
    }
};
