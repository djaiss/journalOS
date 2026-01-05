<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Jobs\UpdateUserLastActivityDate;
use App\Helpers\TextSanitizer;
use Illuminate\Validation\ValidationException;
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
        $this->updateUserLastActivityDate();

        return $this->magicLinkUrl;
    }

    private function validate(): void
    {
        $email = TextSanitizer::plainText($this->email);
        $email = mb_strtolower($email);

        $messages = [];

        if ($email === '') {
            $messages['email'] = 'Email must be plain text.';
        }

        if (mb_strlen($email) > 255) {
            $messages['email'] = 'Email must not be longer than 255 characters.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }

        $this->user = User::query()->where('email', $email)->firstOrFail();
    }

    private function create(): void
    {
        $action = new LoginAction($this->user);
        $action->response(redirect(route('journal.index', absolute: false)));

        $this->magicLinkUrl = MagicLink::create($action, 5)->url;
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
