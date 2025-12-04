<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EmailType;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Models\User;

final class CreateApiKey
{
    public function __construct(
        private readonly User $user,
        private readonly string $label,
    ) {}

    public function execute(): string
    {
        $token = $this->user->createToken($this->label)->plainTextToken;
        $this->log();
        $this->sendEmail();

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

    private function sendEmail(): void
    {
        SendEmail::dispatch(
            emailType: EmailType::API_CREATED,
            user: $this->user,
            parameters: ['label' => $this->label],
        )->onQueue('high');
    }
}
