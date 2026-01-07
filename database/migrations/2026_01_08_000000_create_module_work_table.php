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
        Schema::create('module_work', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->text('worked')->nullable();
            $table->text('work_mode')->nullable();
            $table->text('work_load')->nullable();
            $table->text('work_procrastinated')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });

        $entries = DB::table('journal_entries')
            ->select([
                'id',
                'worked',
                'work_mode',
                'work_load',
                'work_procrastinated',
                'created_at',
                'updated_at',
            ])
            ->whereNotNull('worked')
            ->orWhereNotNull('work_mode')
            ->orWhereNotNull('work_load')
            ->orWhereNotNull('work_procrastinated')
            ->get();

        if ($entries->isNotEmpty()) {
            $entries->each(function (object $entry): void {
                DB::table('module_work')->insert([
                    'journal_entry_id' => $entry->id,
                    'worked' => $entry->worked,
                    'work_mode' => $entry->work_mode,
                    'work_load' => $entry->work_load,
                    'work_procrastinated' => $entry->work_procrastinated,
                    'created_at' => $entry->created_at,
                    'updated_at' => $entry->updated_at,
                ]);
            });
        }

        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn([
                'worked',
                'work_mode',
                'work_load',
                'work_procrastinated',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->text('worked')->nullable();
            $table->text('work_mode')->nullable()->after('worked');
            $table->text('work_load')->nullable()->after('work_mode');
            $table->text('work_procrastinated')->nullable()->after('work_load');
        });

        $entries = DB::table('module_work')
            ->select([
                'journal_entry_id',
                'worked',
                'work_mode',
                'work_load',
                'work_procrastinated',
            ])
            ->get();

        if ($entries->isNotEmpty()) {
            $entries->each(function (object $entry): void {
                DB::table('journal_entries')
                    ->where('id', $entry->journal_entry_id)
                    ->update([
                        'worked' => $entry->worked,
                        'work_mode' => $entry->work_mode,
                        'work_load' => $entry->work_load,
                        'work_procrastinated' => $entry->work_procrastinated,
                    ]);
            });
        }

        Schema::dropIfExists('module_work');
    }
};
