<?php

declare(strict_types=1);

namespace App\Actions;

use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

final class UpdateUserInformation
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
        $this->validate();
        $this->triggerEmailVerification();
        $this->update();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->user;
    }

    private function validate(): void
    {
        $this->email = TextSanitizer::plainText($this->email);
        $this->email = mb_strtolower($this->email);
        $this->firstName = TextSanitizer::plainText($this->firstName);
        $this->lastName = TextSanitizer::plainText($this->lastName);
        $this->nickname = TextSanitizer::nullablePlainText($this->nickname);
        $this->locale = TextSanitizer::plainText($this->locale);

        $messages = [];

        if ($this->email === '') {
            $messages['email'] = 'Email must be plain text.';
        }

        if (mb_strlen($this->email) > 255) {
            $messages['email'] = 'Email must not be longer than 255 characters.';
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

        if ($this->nickname !== null && mb_strlen($this->nickname) > 255) {
            $messages['nickname'] = 'Nickname must not be longer than 255 characters.';
        }

        if ($this->locale === '') {
            $messages['locale'] = 'Locale must be plain text.';
        }

        if (mb_strlen($this->locale) > 255) {
            $messages['locale'] = 'Locale must not be longer than 255 characters.';
        }

        $supportedLocales = config('journalos.supported_locales', []);
        if ($supportedLocales !== [] && ! in_array($this->locale, $supportedLocales, true)) {
            $messages['locale'] = 'Locale is not supported.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
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
