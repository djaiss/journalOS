<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use MagicLink\Actions\LoginAction;
use MagicLink\MagicLink;

/**
 * Create a magic link so the user can log in.
 * This link is valid for 5 minutes.
 */
final class CreateMagicLink
{
    private User $user;

    private string $magicLinkUrl;

    public function __construct(
        private readonly string $email,
    ) {}

    public function execute(): string
    {
        $this->validate();
        $this->create();

        return $this->magicLinkUrl;
    }

    private function validate(): void
    {
        $this->user = User::where('email', $this->email)->firstOrFail();
    }

    private function create(): void
    {
        $action = new LoginAction($this->user);
        $action->response(redirect(route('journal.index', absolute: false)));

        $this->magicLinkUrl = MagicLink::create($action, 5)->url;
    }
}
