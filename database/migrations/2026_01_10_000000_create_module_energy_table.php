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
        Schema::create('module_energy', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->text('energy')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });

        DB::table('journal_entries')
            ->whereNotNull('energy')
            ->orderBy('id')
            ->chunkById(1000, function ($entries): void {
                $timestamp = now();
                $rows = $entries->map(fn ($entry) => [
                    'journal_entry_id' => $entry->id,
                    'energy' => $entry->energy,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ])->all();

                DB::table('module_energy')->insert($rows);
            });

        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('energy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->text('energy')->nullable();
        });

        DB::table('module_energy')
            ->orderBy('id')
            ->chunkById(1000, function ($entries): void {
                foreach ($entries as $entry) {
                    DB::table('journal_entries')
                        ->where('id', $entry->journal_entry_id)
                        ->update(['energy' => $entry->energy]);
                }
            });

        Schema::dropIfExists('module_energy');
    }
};
