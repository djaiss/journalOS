<?php

declare(strict_types=1);

namespace App\Actions;

use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

final readonly class UpdateUserInformation
{
    public function __construct(
        private User $user,
        private string $email,
        private string $firstName,
        private string $lastName,
        private ?string $nickname,
        private string $locale,
        private bool $timeFormat24h,
    ) {}

    /**
     * Update the user information.
     * If the email has changed, we need to send a new verification email to
     * verify the new email address.
     */
    public function execute(): User
    {
        $firstName = TextSanitizer::plainText($this->firstName);
        $lastName = TextSanitizer::plainText($this->lastName);
        $nickname = TextSanitizer::nullablePlainText($this->nickname);
        $locale = TextSanitizer::plainText($this->locale);

        $this->validate($firstName, $lastName, $locale);
        $this->triggerEmailVerification();
        $this->update($firstName, $lastName, $nickname, $locale);
        $this->updateUserLastActivityDate();
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

    private function validate(string $firstName, string $lastName, string $locale): void
    {
        $messages = [];

        if ($firstName === '') {
            $messages['first_name'] = 'First name must be plain text.';
        }

        if ($lastName === '') {
            $messages['last_name'] = 'Last name must be plain text.';
        }

        if ($locale === '') {
            $messages['locale'] = 'Locale must be plain text.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }

    private function update(string $firstName, string $lastName, ?string $nickname, string $locale): void
    {
        $this->user->update([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->email,
            'nickname' => $nickname,
            'locale' => $locale,
            'time_format_24h' => $this->timeFormat24h,
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

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
