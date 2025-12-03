<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Models\User;

final readonly class UpdateTwoFAMethod
{
    public function __construct(
        private User $user,
        private string $preferredMethods,
    ) {}

    /**
     * Update the user's preferred 2FA method.
     */
    public function execute(): User
    {
        $this->update();
        $this->log();

        return $this->user;
    }

    private function update(): void
    {
        $this->user->update([
            'two_factor_preferred_method' => $this->preferredMethods,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            organization: null,
            user: $this->user,
            action: 'update_preferred_method',
            description: 'Updated their preferred 2FA method',
        )->onQueue('low');
    }
}
