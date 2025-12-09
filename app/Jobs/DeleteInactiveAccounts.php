<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\DestroyAccountBecauseInactivity;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class DeleteInactiveAccounts implements ShouldQueue
{
    use Queueable;

    /**
     * Delete all accounts which have been inactive for the last 6 months.
     */
    public function handle(): void
    {
        $users = User::query()->where('auto_delete_account', true)->get();

        foreach ($users as $user) {
            new DestroyAccountBecauseInactivity($user)->execute();
        }
    }
}
