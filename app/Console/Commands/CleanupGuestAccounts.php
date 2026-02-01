<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

final class CleanupGuestAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journalos:cleanup-guest-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired guest accounts after their expiration date';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        User::query()
            ->where('is_guest', true)
            ->where('guest_expires_at', '<', now())
            ->delete();
    }
}
