<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\EmailType;
use App\Jobs\CheckLastLogin;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class CheckLastLoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_user_ip_on_first_login(): void
    {
        $user = User::factory()->create();
        $user->last_used_ip = null;
        $user->save();

        $job = new CheckLastLogin($user, '192.168.1.100');
        $job->handle();

        $user->refresh();

        $this->assertEquals('192.168.1.100', $user->last_used_ip);
    }

    #[Test]
    public function it_updates_user_ip_when_same_ip(): void
    {
        $user = User::factory()->create();
        $user->last_used_ip = '192.168.1.100';
        $user->save();

        $job = new CheckLastLogin($user, '192.168.1.100');
        $job->handle();

        $user->refresh();

        $this->assertEquals('192.168.1.100', $user->last_used_ip);
    }

    #[Test]
    public function it_does_not_send_email_when_ip_has_not_changed(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->last_used_ip = '192.168.1.100';
        $user->save();

        $job = new CheckLastLogin($user, '192.168.1.100');
        $job->handle();

        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_does_not_send_email_on_first_login(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->last_used_ip = null;
        $user->save();

        $job = new CheckLastLogin($user, '192.168.1.100');
        $job->handle();

        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_sends_email_when_ip_changes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->last_used_ip = '192.168.1.100';
        $user->save();

        $job = new CheckLastLogin($user, '192.168.1.200');
        $job->handle();

        Queue::assertPushedOn('high', SendEmail::class, function (SendEmail $job) use ($user): bool {
            return $job->emailType === EmailType::USER_IP_CHANGED
                && $job->user->id === $user->id
                && $job->parameters['ip'] === '192.168.1.200';
        });
    }

    #[Test]
    public function it_updates_ip_when_ip_changes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->last_used_ip = '192.168.1.100';
        $user->save();

        $job = new CheckLastLogin($user, '10.0.0.50');
        $job->handle();

        $user->refresh();

        $this->assertEquals('10.0.0.50', $user->last_used_ip);
    }
}
