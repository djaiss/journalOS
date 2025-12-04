<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EmailType;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Models\User;

final class DestroyApiKey
{
    private string $label;

    public function __construct(
        private readonly User $user,
        private readonly int $tokenId,
    ) {}

    /**
     * Destroy an API key.
     */
    public function execute(): void
    {
        $token = $this->user->tokens()->where('id', $this->tokenId)->first();
        $this->label = $token->name;
        $token->delete();

        $this->log();
        $this->sendEmailToUser();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'api_key_deletion',
            description: 'Deleted an API key',
        )->onQueue('low');
    }

    private function sendEmailToUser(): void
    {
        SendEmail::dispatch(
            emailType: EmailType::API_DESTROYED,
            user: $this->user,
            parameters: [
                'label' => $this->label,
            ],
        )->onQueue('high');
    }
}
