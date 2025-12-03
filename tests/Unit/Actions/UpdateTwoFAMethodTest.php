<?php

declare(strict_types=1);

use App\Actions\UpdateTwoFAMethod;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('updates user 2fa method', function (): void {
    Queue::fake();

    $user = User::factory()->create([
        'two_factor_preferred_method' => 'email',
    ]);

    $updatedUser = (new UpdateTwoFAMethod(
        user: $user,
        preferredMethods: 'sms',
    ))->execute();

    expect($updatedUser->two_factor_preferred_method)->toBe('sms');
    expect($user->fresh()->two_factor_preferred_method)->toBe('sms');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: function (LogUserAction $job) use ($user): bool {
            return $job->user->id === $user->id
                && $job->action === 'update_preferred_method'
                && $job->description === 'Updated their preferred 2FA method';
        },
    );
});
