<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Claim an account for a user.
 */
final readonly class ClaimAccount
{
    public function __construct(
        private User $user,
        private string $email,
        private string $password,
        private string $firstName,
        private string $lastName,
    ) {}

    public function execute(): User
    {
        $this->claim();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->user;
    }

    private function claim(): void
    {
        $this->user->update([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'email_verified_at' => null,
            'trial_ends_at' => now()->addDays(30),
            'is_guest' => false,
            'guest_token' => null,
            'guest_expires_at' => null,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'account_claimed',
            description: 'Claimed the account',
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
