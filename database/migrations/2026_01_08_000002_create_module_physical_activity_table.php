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
        Schema::create('module_physical_activity', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->text('has_done_physical_activity')->nullable();
            $table->text('activity_type')->nullable();
            $table->text('activity_intensity')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });

        $this->migratePhysicalActivityData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->restorePhysicalActivityData();

        Schema::dropIfExists('module_physical_activity');
    }

    private function migratePhysicalActivityData(): void
    {
        if (! Schema::hasColumn('journal_entries', 'has_done_physical_activity')) {
            return;
        }

        DB::table('journal_entries')
            ->select('id', 'has_done_physical_activity', 'activity_type', 'activity_intensity')
            ->where(function ($query): void {
                $query->whereNotNull('has_done_physical_activity')
                    ->orWhereNotNull('activity_type')
                    ->orWhereNotNull('activity_intensity');
            })
            ->orderBy('id')
            ->chunkById(100, function ($entries): void {
                $timestamp = now();

                $payload = $entries->map(fn($entry) => [
                    'journal_entry_id' => $entry->id,
                    'has_done_physical_activity' => $entry->has_done_physical_activity,
                    'activity_type' => $entry->activity_type,
                    'activity_intensity' => $entry->activity_intensity,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ])->all();

                DB::table('module_physical_activity')->insert($payload);
            });
    }

    private function restorePhysicalActivityData(): void
    {
        if (! Schema::hasColumn('journal_entries', 'has_done_physical_activity')) {
            return;
        }

        DB::table('module_physical_activity')
            ->select('journal_entry_id', 'has_done_physical_activity', 'activity_type', 'activity_intensity')
            ->orderBy('id')
            ->chunkById(100, function ($entries): void {
                foreach ($entries as $entry) {
                    DB::table('journal_entries')
                        ->where('id', $entry->journal_entry_id)
                        ->update([
                            'has_done_physical_activity' => $entry->has_done_physical_activity,
                            'activity_type' => $entry->activity_type,
                            'activity_intensity' => $entry->activity_intensity,
                        ]);
                }
            });
    }
};
