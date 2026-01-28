<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\DeleteRelatedAccountData;
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
        $userId = $this->user->id;
        $userCreatedAt = $this->user->created_at;

        $this->deleteRelatedData($userId);
        $this->user->delete();
        $this->sendMail($userCreatedAt);
        $this->logAccountDeletion();
    }

    private function deleteRelatedData(int $userId): void
    {
        DeleteRelatedAccountData::dispatch($userId)->onQueue('low');
    }

    private function sendMail($userCreatedAt): void
    {
        Mail::to(config('services.journalos.account_deletion_notification_email'))
            ->queue(new AccountDestroyed(
                reason: $this->reason,
                activeSince: $userCreatedAt->format('Y-m-d'),
            ));
    }

    private function logAccountDeletion(): void
    {
        AccountDeletionReason::query()->create([
            'reason' => $this->reason,
        ]);
    }
}
