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
 * Create an account for a user.
 */
final class CreateAccount
{
    private User $user;

    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly string $firstName,
        private readonly string $lastName,
    ) {}

    public function execute(): User
    {
        $this->validate();
        $this->create();
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

    private function create(): void
    {
        $this->user = User::query()->create([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'trial_ends_at' => now()->addDays(30),
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'account_created',
            description: 'Created an account',
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
