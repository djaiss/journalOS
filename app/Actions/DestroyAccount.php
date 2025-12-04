<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\AccountDestroyed;
use App\Models\AccountDeletionReason;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Delete a user account.
 */
final readonly class DestroyAccount
{
    public function __construct(
        private User $user,
        private string $reason,
    ) {}

    public function execute(): void
    {
        $this->user->delete();
        $this->sendMail();
        $this->logAccountDeletion();
    }

    private function sendMail(): void
    {
        Mail::to(config('memoir.account_deletion_notification_email'))
            ->queue(new AccountDestroyed(
                reason: $this->reason,
                activeSince: $this->user->created_at->format('Y-m-d'),
            ));
    }

    private function logAccountDeletion(): void
    {
        AccountDeletionReason::create([
            'reason' => $this->reason,
        ]);
    }
}
