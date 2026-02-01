<?php

declare(strict_types = 1);

namespace Tests\Unit\Jobs;

use App\Enums\EmailType;
use App\Jobs\SendEmail;
use App\Mail\ApiKeyDestroyed;
use App\Models\EmailSent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Resend\Email;
use Tests\TestCase;

final class SendEmailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_email_the_traditional_way(): void
    {
        Config::set('app.use_resend', false);
        Config::set('mail.from.address', 'noreply@example.com');
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
        ]);

        $job = new SendEmail(
            emailType: EmailType::API_DESTROYED,
            user: $user,
            parameters: ['label' => '123'],
        );

        $job->handle();

        Mail::assertQueued(ApiKeyDestroyed::class, function (ApiKeyDestroyed $mail) use ($user): bool {
            return $mail->hasTo($user->email) && $mail->label === '123';
        });

        $emailSent = EmailSent::latest()->first();
        $this->assertEquals(EmailType::API_DESTROYED->value, $emailSent->email_type);
        $this->assertEquals('michael.scott@dundermifflin.com', $emailSent->email_address);
        $this->assertEquals('API key removed', $emailSent->subject);
    }

    #[Test]
    public function it_sends_email_with_resend_facade(): void
    {
        Config::set('app.use_resend', true);
        Config::set('mail.from.address', 'noreply@example.com');

        $resendMock = Mockery::mock();
        $emailsMock = Mockery::mock(\Resend\Service\Email::class);

        $emailsMock
            ->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($args) {
                return (
                    $args['from'] === 'noreply@example.com'
                    && $args['to'] === ['michael.scott@dundermifflin.com']
                    && $args['subject'] === 'API key removed'
                    && is_string($args['html'])
                    && mb_strlen($args['html']) > 0
                );
            }))
            ->andReturn(Email::from(['id' => 'resend-uuid-12345']));

        // The facade accesses the emails property directly, not method
        $resendMock->emails = $emailsMock;

        // Replace the Resend service binding with our mock
        app()->instance('resend', $resendMock);

        $user = User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
        ]);

        $job = new SendEmail(
            emailType: EmailType::API_DESTROYED,
            user: $user,
            parameters: ['label' => '123'],
        );

        $job->handle();

        $emailSent = EmailSent::latest()->first();
        $this->assertEquals(EmailType::API_DESTROYED->value, $emailSent->email_type);
        $this->assertEquals('michael.scott@dundermifflin.com', $emailSent->email_address);
        $this->assertEquals('API key removed', $emailSent->subject);
        $this->assertEquals('resend-uuid-12345', $emailSent->uuid);
    }
}
