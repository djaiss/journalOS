<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

final readonly class UpdateUserInformation
{
    public function __construct(
        private readonly User $user,
        private readonly string $email,
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly ?string $nickname,
        private readonly string $locale,
    ) {}

    /**
     * Update the user information.
     * If the email has changed, we need to send a new verification email to
     * verify the new email address.
     */
    public function execute(): User
    {
        $this->triggerEmailVerification();
        $this->update();
        $this->log();

        return $this->user;
    }

    private function triggerEmailVerification(): void
    {
        if ($this->user->email !== $this->email) {
            $this->user->email_verified_at = null;
            $this->user->save();
            event(new Registered($this->user));
        }
    }

    private function update(): void
    {
        $this->user->update([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'locale' => $this->locale,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'personal_profile_update',
            description: 'Updated their personal profile',
        )->onQueue('low');
    }
}
