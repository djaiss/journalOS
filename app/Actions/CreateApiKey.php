<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EmailType;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class CreateApiKey
{
    public function __construct(
        private User $user,
        private string $label,
    ) {}

    public function execute(): string
    {
        $this->validate();

        $token = $this->user->createToken($this->label)->plainTextToken;
        $this->log();
        $this->sendEmail();
        $this->updateUserLastActivityDate();

        return $token;
    }

    private function validate(): void
    {
        $this->label = TextSanitizer::plainText($this->label);

        $messages = [];

        if ($this->label === '') {
            $messages['label'] = 'API key label must be plain text.';
        }

        if (mb_strlen($this->label) < 3) {
            $messages['label'] = 'API key label must be at least 3 characters.';
        }

        if (mb_strlen($this->label) > 255) {
            $messages['label'] = 'API key label must not be longer than 255 characters.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'api_key_creation',
            description: 'Created an API key',
        )->onQueue('low');
    }

    private function sendEmail(): void
    {
        SendEmail::dispatch(
            emailType: EmailType::API_CREATED,
            user: $this->user,
            parameters: ['label' => $this->label],
        )->onQueue('high');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
