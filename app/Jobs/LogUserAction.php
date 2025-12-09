<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Journal;
use App\Models\Log;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class LogUserAction implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public ?Journal $journal = null,
        public string $action = '',
        public string $description = '',
    ) {}

    /**
     * Log the user action in the logs table.
     */
    public function handle(): void
    {
        Log::query()->create([
            'user_id' => $this->user->id,
            'journal_id' => $this->journal?->id,
            'journal_name' => $this->journal?->name,
            'action' => $this->action,
            'description' => $this->description,
        ]);
    }
}
