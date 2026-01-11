<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ClaimAccount;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ClaimAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_claims_a_guest_account(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 12, 16, 9, 0, 0));

        $guest = User::factory()->create([
            'email' => 'guest+123@example.com',
            'first_name' => 'Guest',
            'last_name' => 'User',
            'password' => Hash::make('old-password'),
            'is_guest' => true,
            'guest_token' => fake()->uuid(),
            'guest_expires_at' => Carbon::create(2025, 12, 17, 9, 0, 0),
        ]);

        $user = (new ClaimAccount(
            user: $guest,
            email: 'pam.beesly@dundermifflin.com',
            password: 'new-password',
            firstName: 'Pam',
            lastName: 'Beesly',
        ))->execute();

        $user->refresh();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Pam', $user->first_name);
        $this->assertEquals('Beesly', $user->last_name);
        $this->assertEquals('pam.beesly@dundermifflin.com', $user->email);
        $this->assertTrue(Hash::check('new-password', $user->password));
        $this->assertNull($user->email_verified_at);
        $this->assertFalse($user->is_guest);
        $this->assertNull($user->guest_token);
        $this->assertNull($user->guest_expires_at);
        $this->assertEquals('2026-01-15 09:00:00', $user->trial_ends_at?->format('Y-m-d H:i:s'));

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'account_claimed'
                    && $job->user->id === $user->id
                    && $job->description === 'Claimed the account';
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
