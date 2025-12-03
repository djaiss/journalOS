<?php

declare(strict_types=1);

use App\Jobs\SendEmail;
use App\Models\User;
use App\Enums\EmailType;
use Illuminate\Support\Facades\Queue;

it('renders the login screen', function (): void {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

it('authenticates a user', function (): void {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('organization.index', absolute: false));
});

it('does not authenticate a user with invalid password', function (): void {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

it('sends an email on failed login', function (): void {
    Queue::fake();

    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    Queue::assertPushed(SendEmail::class, function (SendEmail $job) use ($user): bool {
        return $job->emailType === EmailType::LOGIN_FAILED && $job->user->id === $user->id;
    });
});

it('logs out a user', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
