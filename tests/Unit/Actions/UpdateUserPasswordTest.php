<?php

declare(strict_types=1);

use App\Jobs\LogUserAction;
use App\Models\User;
use App\Actions\UpdateUserPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;

it('updates user password', function (): void {
    Queue::fake();

    $user = User::factory()->create([
        'password' => Hash::make('current-password'),
    ]);

    $updatedUser = (new UpdateUserPassword(
        user: $user,
        currentPassword: 'current-password',
        newPassword: 'new-password',
    ))->execute();

    expect(Hash::check('new-password', $updatedUser->fresh()->password))->toBeTrue();

    expect($updatedUser)->toBeInstanceOf(User::class);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: function (LogUserAction $job) use ($user): bool {
            return $job->action === 'update_user_password'
                && $job->user->id === $user->id
                && $job->description === 'Updated their password';
        },
    );
});

it('throws exception when current password is incorrect', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('current-password'),
    ]);

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Current password is incorrect');

    (new UpdateUserPassword(
        user: $user,
        currentPassword: Hash::make('wrong-password'),
        newPassword: 'new-password',
    ))->execute();
});
