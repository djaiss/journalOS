<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Create a guest account for an unlogged user.
 * Those accounts are temporary and expire after 7 days.
 */
final class CreateGuestAccount
{
    private User $user;

    public function execute(): User
    {
        $this->create();
        $this->createFirstJournal();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->user;
    }

    private function create(): void
    {
        // generate a unique email for the guest user
        $email = 'guest+' . uniqid() . '@' . parse_url((string) config('app.url'), PHP_URL_HOST);

        $this->user = User::query()->create([
            'first_name' => 'James',
            'last_name' => 'Bond',
            'email' => $email,
            'password' => Hash::make(Str::uuid()->toString()),
            'is_guest' => true,
            'guest_token' => Str::uuid()->toString(),
            'guest_expires_at' => now()->addDays(7),
            'email_verified_at' => now(),
            'trial_ends_at' => now()->addDays(37),
        ]);
    }

    private function createFirstJournal(): void
    {
        new CreateJournal(
            user: $this->user,
            name: 'My first journal',
        )->execute();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'account_created',
            description: 'Created an account',
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
