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
        Schema::create('module_shopping', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->index('journal_entry_id');
            $table->string('category')->default(ModuleType::MOVEMENT_PLACES->value);
            $table->text('has_shopped_today')->nullable();
            $table->text('shopping_type')->nullable();
            $table->text('shopping_intent')->nullable();
            $table->text('shopping_context')->nullable();
            $table->text('shopping_for')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_shopping');
    }
};
