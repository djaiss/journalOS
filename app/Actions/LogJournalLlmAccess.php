<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Models\Journal;
use App\Models\JournalLlmAccessLog;

final readonly class LogJournalLlmAccess
{
    public function __construct(
        private Journal $journal,
        private string $requestUrl,
        private int $year,
        private ?int $month,
        private ?int $day,
    ) {}

    public function execute(): JournalLlmAccessLog
    {
        return JournalLlmAccessLog::query()->create([
            'journal_id' => $this->journal->id,
            'requested_year' => $this->year,
            'requested_month' => $this->month,
            'requested_day' => $this->day,
            'request_url' => $this->requestUrl,
        ]);
    }
}
