<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Helpers\TextSanitizer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Claim an account for a user.
 */
final class ClaimAccount
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
        $this->validate();
        $this->claim();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->user;
    }

    private function validate(): void
    {
        $this->email = TextSanitizer::plainText($this->email);
        $this->email = mb_strtolower($this->email);
        $this->password = TextSanitizer::plainText($this->password);
        $this->firstName = TextSanitizer::plainText($this->firstName);
        $this->lastName = TextSanitizer::plainText($this->lastName);

        $messages = [];

        if ($this->email === '') {
            $messages['email'] = 'Email must be plain text.';
        }

        if (mb_strlen($this->email) > 255) {
            $messages['email'] = 'Email must not be longer than 255 characters.';
        }

        if ($this->password === '') {
            $messages['password'] = 'Password must be plain text.';
        }

        if (mb_strlen($this->password) > 255) {
            $messages['password'] = 'Password must not be longer than 255 characters.';
        }

        if ($this->firstName === '') {
            $messages['first_name'] = 'First name must be plain text.';
        }

        if (mb_strlen($this->firstName) > 255) {
            $messages['first_name'] = 'First name must not be longer than 255 characters.';
        }

        if ($this->lastName === '') {
            $messages['last_name'] = 'Last name must be plain text.';
        }

        if (mb_strlen($this->lastName) > 255) {
            $messages['last_name'] = 'Last name must not be longer than 255 characters.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }

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
