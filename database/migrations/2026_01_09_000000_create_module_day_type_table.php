<?php

declare(strict_types=1);

use App\Models\JournalEntry;
use App\Models\ModuleDayType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('module_day_type', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->text('day_type')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });

        JournalEntry::query()
            ->whereNotNull('day_type')
            ->select(['id', 'day_type'])
            ->chunkById(100, function ($entries): void {
                foreach ($entries as $entry) {
                    ModuleDayType::query()->create([
                        'journal_entry_id' => $entry->id,
                        'day_type' => $entry->day_type,
                    ]);
                }
            });

        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('day_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->text('day_type')->nullable();
        });

        ModuleDayType::query()
            ->select(['id', 'journal_entry_id', 'day_type'])
            ->chunkById(100, function ($modules): void {
                foreach ($modules as $module) {
                    $entry = JournalEntry::query()->find($module->journal_entry_id);

                    if ($entry === null) {
                        continue;
                    }

                    $entry->day_type = $module->day_type;
                    $entry->save();
                }
            });

        Schema::dropIfExists('module_day_type');
    }
};
