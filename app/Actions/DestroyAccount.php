<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\AccountDestroyed;
use App\Models\AccountDeletionReason;
use App\Models\User;
use App\Helpers\TextSanitizer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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
        $reason = $this->validate();

        $this->user->delete();
        $this->sendMail($reason);
        $this->logAccountDeletion($reason);
    }

    private function validate(): string
    {
        $reason = TextSanitizer::plainText($this->reason);

        $messages = [];

        if ($reason === '') {
            $messages['reason'] = 'Reason must be plain text.';
        }

        if (mb_strlen($reason) < 3) {
            $messages['reason'] = 'Reason must be at least 3 characters.';
        }

        if (mb_strlen($reason) > 255) {
            $messages['reason'] = 'Reason must not be longer than 255 characters.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }

        return $reason;
    }

    private function sendMail(string $reason): void
    {
        Mail::to(config('journalos.account_deletion_notification_email'))
            ->queue(new AccountDestroyed(
                reason: $reason,
                activeSince: $this->user->created_at->format('Y-m-d'),
            ));
    }

    private function logAccountDeletion(string $reason): void
    {
        AccountDeletionReason::query()->create([
            'reason' => $reason,
        ]);
    }
}
