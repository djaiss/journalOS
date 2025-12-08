<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateTwoFAMethod;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class UpdateTwoFAMethodTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_user_2fa_method(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'two_factor_preferred_method' => 'email',
        ]);

        $updatedUser = (new UpdateTwoFAMethod(
            user: $user,
            preferredMethods: 'sms',
        ))->execute();

        $this->assertEquals('sms', $updatedUser->two_factor_preferred_method);
        $this->assertEquals('sms', $user->fresh()->two_factor_preferred_method);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->user->id === $user->id
                    && $job->action === 'update_preferred_method'
                    && $job->description === 'Updated their preferred 2FA method';
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
}
