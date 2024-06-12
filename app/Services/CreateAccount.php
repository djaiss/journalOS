<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Create an account for the new user.
 */
class CreateAccount extends BaseService
{
    private User $user;

    public function __construct(
        public string $email,
        public string $password,
        public string $name,
    ) {
    }

    public function execute(): User
    {
        $this->createUser();

        return $this->user;
    }

    private function createUser(): void
    {
        $this->user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
    }
}
