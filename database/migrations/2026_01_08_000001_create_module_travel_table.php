<?php

declare(strict_types=1);

use App\Models\JournalEntry;
use App\Models\ModuleTravel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('module_travel', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->text('has_traveled_today')->nullable();
            $table->text('travel_details')->nullable();
            $table->text('travel_mode')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });

        $now = now();

        JournalEntry::query()
            ->select('id', 'has_traveled_today', 'travel_details', 'travel_mode')
            ->where(function ($query): void {
                $query->whereNotNull('has_traveled_today')
                    ->orWhereNotNull('travel_details')
                    ->orWhereNotNull('travel_mode');
            })
            ->chunkById(100, function ($entries) use ($now): void {
                $payload = $entries->map(fn ($entry) => [
                    'journal_entry_id' => $entry->id,
                    'has_traveled_today' => $entry->has_traveled_today,
                    'travel_details' => $entry->travel_details,
                    'travel_mode' => $entry->travel_mode,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                ModuleTravel::query()->insert($payload);
            });

        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('travel_mode');
            $table->dropColumn('travel_details');
            $table->dropColumn('has_traveled_today');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->text('has_traveled_today')->nullable()->after('work_procrastinated');
            $table->text('travel_details')->nullable()->after('has_traveled_today');
            $table->text('travel_mode')->nullable()->after('travel_details');
        });

        ModuleTravel::query()
            ->select('journal_entry_id', 'has_traveled_today', 'travel_details', 'travel_mode')
            ->chunkById(100, function ($entries): void {
                foreach ($entries as $entry) {
                    JournalEntry::query()
                        ->whereKey($entry->journal_entry_id)
                        ->update([
                            'has_traveled_today' => $entry->has_traveled_today,
                            'travel_details' => $entry->travel_details,
                            'travel_mode' => $entry->travel_mode,
                        ]);
                }
            });

        Schema::dropIfExists('module_travel');
    }
};
