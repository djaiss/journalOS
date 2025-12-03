<?php

declare(strict_types=1);

use App\Enums\EmailType;
use App\Jobs\SendEmail;
use App\Mail\ApiKeyDestroyed;
use App\Models\EmailSent;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Resend\Laravel\Facades\Resend;

it('sends email the traditional way', function (): void {
    Config::set('collectos.use_resend', false);
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
        return $mail->hasTo($user->email)
            && $mail->label === '123';
    });

    $emailSent = EmailSent::latest()->first();
    expect($emailSent->email_type)->toBe(EmailType::API_DESTROYED->value);
    expect($emailSent->email_address)->toBe('michael.scott@dundermifflin.com');
    expect($emailSent->subject)->toBe('API key removed');
});

it('sends email with resend facade', function (): void {
    Config::set('collectos.use_resend', true);
    Config::set('mail.from.address', 'noreply@example.com');

    $resendMock = Mockery::mock();
    $emailsMock = Mockery::mock(\Resend\Service\Email::class);

    $emailsMock->shouldReceive('send')
        ->once()
        ->with(Mockery::on(function ($args) {
            return $args['from'] === 'noreply@example.com'
                   && $args['to'] === ['michael.scott@dundermifflin.com']
                   && $args['subject'] === 'API key removed'
                   && is_string($args['html'])
                   && mb_strlen($args['html']) > 0;
        }))
        ->andReturn(\Resend\Email::from(['id' => 'resend-uuid-12345']));

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
    expect($emailSent->email_type)->toBe(EmailType::API_DESTROYED->value);
    expect($emailSent->email_address)->toBe('michael.scott@dundermifflin.com');
    expect($emailSent->subject)->toBe('API key removed');
    expect($emailSent->uuid)->toBe('resend-uuid-12345');
});
