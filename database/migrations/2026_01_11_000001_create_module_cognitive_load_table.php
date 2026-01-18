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
        Schema::create('module_cognitive_load', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->index('journal_entry_id');
            $table->string('category')->default(ModuleType::MIND_EMOTION->value);
            $table->text('cognitive_load')->nullable();
            $table->text('primary_source')->nullable();
            $table->text('load_quality')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_cognitive_load');
    }
};
