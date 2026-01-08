<?php

declare(strict_types=1);

use App\Models\JournalEntry;
use App\Models\ModuleSexualActivity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('module_sexual_activity', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->text('had_sexual_activity')->nullable();
            $table->text('sexual_activity_type')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });

        JournalEntry::query()
            ->where(function ($query): void {
                $query
                    ->whereNotNull('had_sexual_activity')
                    ->orWhereNotNull('sexual_activity_type');
            })
            ->orderBy('id')
            ->chunkById(100, function ($entries): void {
                $payload = [];

                foreach ($entries as $entry) {
                    $payload[] = [
                        'journal_entry_id' => $entry->id,
                        'had_sexual_activity' => $entry->getRawOriginal('had_sexual_activity'),
                        'sexual_activity_type' => $entry->getRawOriginal('sexual_activity_type'),
                        'created_at' => $entry->created_at,
                        'updated_at' => $entry->updated_at,
                    ];
                }

                if ($payload !== []) {
                    ModuleSexualActivity::query()->insert($payload);
                }
            }, column: 'id');

        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn(['had_sexual_activity', 'sexual_activity_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->text('had_sexual_activity')->nullable();
            $table->text('sexual_activity_type')->nullable();
        });

        ModuleSexualActivity::query()
            ->orderBy('id')
            ->chunkById(100, function ($modules): void {
                foreach ($modules as $module) {
                    JournalEntry::query()
                        ->whereKey($module->journal_entry_id)
                        ->update([
                            'had_sexual_activity' => $module->getRawOriginal('had_sexual_activity'),
                            'sexual_activity_type' => $module->getRawOriginal('sexual_activity_type'),
                        ]);
                }
            }, column: 'id');

        Schema::dropIfExists('module_sexual_activity');
    }
};
