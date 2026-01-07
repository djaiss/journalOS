<?php

declare(strict_types=1);

use App\Models\JournalEntry;
use App\Models\ModuleHealth;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('journal_entries', 'health')) {
            return;
        }

        JournalEntry::query()
            ->whereNotNull('health')
            ->orderBy('id')
            ->chunkById(100, function ($entries): void {
                foreach ($entries as $entry) {
                    ModuleHealth::query()->create([
                        'journal_entry_id' => $entry->id,
                        'health' => $entry->health,
                    ]);
                }
            });

        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('health');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('journal_entries', 'health')) {
            return;
        }

        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->text('health')->nullable()->after('activity_type');
        });

        ModuleHealth::query()
            ->orderBy('id')
            ->chunkById(100, function ($modules): void {
                foreach ($modules as $module) {
                    JournalEntry::query()
                        ->whereKey($module->journal_entry_id)
                        ->update(['health' => $module->health]);
                }
            });
    }
};
