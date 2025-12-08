<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateUserPassword;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use InvalidArgumentException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class UpdateUserPasswordTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_user_password(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'password' => Hash::make('current-password'),
        ]);

        $updatedUser = (new UpdateUserPassword(
            user: $user,
            currentPassword: 'current-password',
            newPassword: 'new-password',
        ))->execute();

        $this->assertTrue(Hash::check('new-password', $updatedUser->fresh()->password));

        $this->assertInstanceOf(User::class, $updatedUser);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'update_user_password'
                    && $job->user->id === $user->id
                    && $job->description === 'Updated their password';
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );
    }

    #[Test]
    public function it_throws_exception_when_current_password_is_incorrect(): void
    {
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
    }
}
