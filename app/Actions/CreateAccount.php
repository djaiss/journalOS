<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        $this->create();
        $this->log();

        return $this->user;
    }

    private function create(): void
    {
        $this->user = User::create([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'password' => Hash::make($this->password),
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
}
