<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Helpers\TextSanitizer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Illuminate\Validation\ValidationException;

final class UpdateUserPassword
{
    public function __construct(
        private User $user,
        private string $currentPassword,
        private string $newPassword,
    ) {}

    /**
     * Update the user password.
     */
    public function execute(): User
    {
        $this->validate();
        $this->update();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->user;
    }

    private function validate(): void
    {
        $this->currentPassword = TextSanitizer::plainText($this->currentPassword);
        $this->newPassword = TextSanitizer::plainText($this->newPassword);

        $messages = [];

        if ($this->currentPassword === '') {
            $messages['current_password'] = 'Current password must be plain text.';
        }

        if (mb_strlen($this->currentPassword) > 255) {
            $messages['current_password'] = 'Current password must not be longer than 255 characters.';
        }

        if ($this->newPassword === '') {
            $messages['new_password'] = 'New password must be plain text.';
        }

        if (mb_strlen($this->newPassword) > 255) {
            $messages['new_password'] = 'New password must not be longer than 255 characters.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }

        if (! Hash::check($this->currentPassword, $this->user->password)) {
            throw new InvalidArgumentException('Current password is incorrect');
        }
    }

    private function update(): void
    {
        $this->user->update([
            'password' => Hash::make($this->newPassword),
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'update_user_password',
            description: 'Updated their password',
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
