<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class DeleteRelatedAccountData implements ShouldQueue
{
    use Queueable;

    private const int BATCH_SIZE = 1000;

    /**
     * Add every table that has a user_id FK here.
     */
    private const array USER_RELATED_TABLES = [
        'books' => 'user_id',
        'emails_sent' => 'user_id',
    ];

    public function __construct(
        public int $userId,
    ) {}

    public function handle(): void
    {
        $this->deleteJournalsAndRelatedData();
        $this->deleteUserRelatedData();
        $this->deleteLogs();
    }

    private function deleteJournalsAndRelatedData(): void
    {
        DB::table('journals')
            ->where('user_id', $this->userId)
            ->select('id')
            ->orderedChunkById(
                self::BATCH_SIZE,
                function (Collection $rows): void {
                    $journalIds = $rows->pluck('id')->all();

                    foreach ($journalIds as $journalId) {
                        DeleteRelatedJournalData::dispatch($journalId)->onQueue('low');
                    }

                    DB::table('journals')
                        ->whereIn('id', $journalIds)
                        ->delete();
                },
                column: 'id',
            );
    }

    private function deleteUserRelatedData(): void
    {
        DB::transaction(function (): void {
            foreach (self::USER_RELATED_TABLES as $table => $fkColumn) {
                DB::table($table)
                    ->where($fkColumn, $this->userId)
                    ->delete();
            }
        });
    }

    private function deleteLogs(): void
    {
        DB::table('logs')
            ->where('user_id', $this->userId)
            ->select('id')
            ->orderedChunkById(
                self::BATCH_SIZE,
                function (Collection $rows): void {
                    $logIds = $rows->pluck('id')->all();

                    DB::transaction(function () use ($logIds): void {
                        DB::table('logs')
                            ->whereIn('id', $logIds)
                            ->delete();
                    });
                },
                column: 'id',
            );
    }
}
