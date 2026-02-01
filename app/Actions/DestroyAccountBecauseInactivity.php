<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\Mail;
use App\Mail\AccountAutomaticallyDestroyed;
use App\Models\User;

/**
 * Delete an account if there is no activity for all users after a period of
 * time.
 */
final readonly class DestroyAccountBecauseInactivity
{
    public function __construct(
        private User $user,
    ) {}

    public function execute(): void
    {
        if ($this->user->last_activity_at === null) {
            return;
        }

        // Check if the user has been inactive for 6 months
        if ($this->user->last_activity_at->diffInMonths(now()) >= 6) {
            $this->user->delete();

            Mail::to(config('app.account_deletion_notification_email'))
                ->queue(new AccountAutomaticallyDestroyed(
                    age: $this->user->created_at->diffInMonths(now()) . ' months',
                ));
        }
    }
}
