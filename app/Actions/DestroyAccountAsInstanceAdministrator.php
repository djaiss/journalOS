<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Exception;

final readonly class DestroyAccountAsInstanceAdministrator
{
    public function __construct(
        private User $user,
        private User $account,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->account->delete();
    }

    private function validate(): void
    {
        if (! $this->user->is_instance_admin) {
            throw new Exception('Account is not an instance administrator');
        }
    }
}
