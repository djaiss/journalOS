<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateUserInformation;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UpdateUserInformationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_updates_user_information(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Ross',
            'last_name' => 'Geller',
            'email' => 'michael.scott@dundermifflin.com',
        ]);

        $updatedUser = new UpdateUserInformation(
            user: $user,
            email: 'michael.scott@dundermifflin.com',
            firstName: 'Michael',
            lastName: 'Scott',
            nickname: 'Mike',
            locale: 'fr',
            timeFormat24h: false,
        )->execute();

        $this->assertInstanceOf(User::class, $updatedUser);

        // Verify database directly
        $userInDb = User::find($updatedUser->id);
        $this->assertEquals($updatedUser->id, $userInDb->id);
        $this->assertEquals('michael.scott@dundermifflin.com', $userInDb->email);
        $this->assertEquals('Michael', $userInDb->first_name);
        $this->assertEquals('Scott', $userInDb->last_name);
        $this->assertEquals('Mike', $userInDb->nickname);
        $this->assertEquals('fr', $userInDb->locale);
        $this->assertFalse($userInDb->time_format_24h);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => $job->action === 'personal_profile_update' && $job->user->id === $user->id,
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job) => $job->user->id === $user->id,
        );
    }

    #[Test]
    public function it_triggers_email_verification_when_email_changes(): void
    {
        Event::fake();

        $user = User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
            'email_verified_at' => now(),
        ]);

        new UpdateUserInformation(
            user: $user,
            email: 'dwight.schrute@dundermifflin.com',
            firstName: 'Dwight',
            lastName: 'Schrute',
            nickname: 'Dwight',
            locale: 'fr',
            timeFormat24h: true,
        )->execute();

        Event::assertDispatched(Registered::class);
        $this->assertNull($user->refresh()->email_verified_at);
    }

    #[Test]
    public function it_does_not_trigger_email_verification_when_email_stays_same(): void
    {
        Event::fake();

        $user = User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
            'email_verified_at' => now(),
        ]);

        new UpdateUserInformation(
            user: $user,
            email: 'michael.scott@dundermifflin.com',
            firstName: 'Dwight',
            lastName: 'Schrute',
            nickname: 'Dwight',
            locale: 'fr',
            timeFormat24h: true,
        )->execute();

        Event::assertNotDispatched(Registered::class);
        $this->assertNotNull($user->refresh()->email_verified_at);
    }
}
