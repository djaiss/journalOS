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
        Schema::create('module_social_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->string('category')->default(ModuleType::RELATIONSHIPS_SOCIAL_LIFE->value);
            $table->text('event_type')->nullable();
            $table->text('tone')->nullable();
            $table->text('duration')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_social_events');
    }
};
