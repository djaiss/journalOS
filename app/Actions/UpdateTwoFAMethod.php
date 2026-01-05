<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Helpers\TextSanitizer;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class UpdateTwoFAMethod
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
        $this->validate();
        $this->update();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->user;
    }

    private function validate(): void
    {
        $this->preferredMethods = TextSanitizer::plainText($this->preferredMethods);

        $messages = [];

        if ($this->preferredMethods === '') {
            $messages['preferred_method'] = 'Preferred method must be plain text.';
        }

        if (mb_strlen($this->preferredMethods) > 255) {
            $messages['preferred_method'] = 'Preferred method must not be longer than 255 characters.';
        }

        if ($messages === [] && ! in_array($this->preferredMethods, ['none', 'authenticator', 'email'], true)) {
            $messages['preferred_method'] = 'Preferred method is not supported.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
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
            user: $this->user,
            journal: null,
            action: 'update_preferred_method',
            description: 'Updated their preferred 2FA method',
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
