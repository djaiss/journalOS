<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Log;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class LogUserAction implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?Organization $organization,
        public User $user,
        public string $action,
        public string $description,
    ) {}

    /**
     * Log the user action in the logs table.
     */
    public function handle(): void
    {
        Log::create([
            'organization_id' => $this->organization?->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->getFullName(),
            'action' => $this->action,
            'description' => $this->description,
        ]);
    }
}
