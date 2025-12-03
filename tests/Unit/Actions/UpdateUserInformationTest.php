<?php

declare(strict_types=1);

use App\Jobs\LogUserAction;
use App\Models\User;
use App\Actions\UpdateUserInformation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

it('updates user information', function (): void {
    Queue::fake();

    $user = User::factory()->create([
        'first_name' => 'Ross',
        'last_name' => 'Geller',
        'email' => 'michael.scott@dundermifflin.com',
    ]);

    $updatedUser = (new UpdateUserInformation(
        user: $user,
        email: 'michael.scott@dundermifflin.com',
        firstName: 'Michael',
        lastName: 'Scott',
        nickname: 'Mike',
        locale: 'fr',
    ))->execute();

    expect($updatedUser)
        ->toBeInstanceOf(User::class);

    // Verify database directly
    $userInDb = User::find($updatedUser->id);
    expect($userInDb)
        ->id->toBe($updatedUser->id)
        ->email->toBe('michael.scott@dundermifflin.com')
        ->first_name->toBe('Michael')
        ->last_name->toBe('Scott')
        ->nickname->toBe('Mike')
        ->locale->toBe('fr');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: function (LogUserAction $job) use ($user): bool {
            return $job->action === 'personal_profile_update' && $job->user->id === $user->id;
        },
    );
});

it('triggers email verification when email changes', function (): void {
    Event::fake();
    Queue::fake();

    $user = User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
        'email_verified_at' => now(),
    ]);

    (new UpdateUserInformation(
        user: $user,
        email: 'dwight.schrute@dundermifflin.com',
        firstName: 'Dwight',
        lastName: 'Schrute',
        nickname: 'Dwight',
        locale: 'fr',
    ))->execute();

    Event::assertDispatched(Registered::class);
    expect($user->refresh()->email_verified_at)->toBeNull();
});

it('does not trigger email verification when email stays same', function (): void {
    Event::fake();
    Queue::fake();

    $user = User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
        'email_verified_at' => now(),
    ]);

    (new UpdateUserInformation(
        user: $user,
        email: 'michael.scott@dundermifflin.com',
        firstName: 'Dwight',
        lastName: 'Schrute',
        nickname: 'Dwight',
        locale: 'fr',
    ))->execute();

    Event::assertNotDispatched(Registered::class);
    expect($user->refresh()->email_verified_at)->not->toBeNull();
});
