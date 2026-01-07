<?php

declare(strict_types=1);

use App\Models\JournalEntry;
use App\Models\ModuleMood;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        JournalEntry::query()
            ->whereNotNull('mood')
            ->orderBy('id')
            ->chunkById(100, function ($entries): void {
                foreach ($entries as $entry) {
                    ModuleMood::query()->updateOrCreate(
                        ['journal_entry_id' => $entry->id],
                        ['mood' => $entry->mood],
                    );
                }
            });

        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('mood');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->text('mood')->nullable()->after('health');
        });

        ModuleMood::query()
            ->whereNotNull('mood')
            ->orderBy('id')
            ->chunkById(100, function ($modules): void {
                foreach ($modules as $module) {
                    $entry = JournalEntry::query()->find($module->journal_entry_id);
                    if ($entry === null) {
                        continue;
                    }

                    $entry->mood = $module->mood;
                    $entry->save();
                }
            });
    }
};
