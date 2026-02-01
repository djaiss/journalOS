<?php

declare(strict_types=1);

use App\Enums\ModuleType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('module_reading', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->string('category')->default(ModuleType::MIND_EMOTION->value);
            $table->text('did_read_today')->nullable();
            $table->text('reading_amount')->nullable();
            $table->text('mental_state')->nullable();
            $table->text('reading_feel')->nullable();
            $table->text('want_continue')->nullable();
            $table->text('reading_limit')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_reading');
    }
};
