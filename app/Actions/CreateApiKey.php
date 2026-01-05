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

final readonly class CreateApiKey
{
    public function __construct(
        private User $user,
        private string $label,
    ) {}

    public function execute(): string
    {
        $label = TextSanitizer::plainText($this->label);

        if ($label === '') {
            throw ValidationException::withMessages([
                'label' => 'API key label must be plain text.',
            ]);
        }

        $token = $this->user->createToken($label)->plainTextToken;
        $this->log();
        $this->sendEmail($label);
        $this->updateUserLastActivityDate();

        return $token;
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

    private function sendEmail(string $label): void
    {
        SendEmail::dispatch(
            emailType: EmailType::API_CREATED,
            user: $this->user,
            parameters: ['label' => $label],
        )->onQueue('high');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
