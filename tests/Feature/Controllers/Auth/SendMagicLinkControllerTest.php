<?php

declare(strict_types=1);

use App\Models\User;

it('displays the magic link request form', function (): void {
    $response = $this->get(route('magic.link'));

    $response->assertStatus(200);
    $response->assertViewIs('auth.request-magic-link');
});

it('sends magic link when user exists', function (): void {
    User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
    ]);

    $response = $this->json('POST', route('magic.link.store'), [
        'email' => 'michael.scott@dundermifflin.com',
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('auth.magic-link-sent');
});

it('shows success view even when user not found', function (): void {
    $response = $this->json('POST', route('magic.link.store'), [
        'email' => 'not.found@dundermifflin.com',
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('auth.magic-link-sent');
});

it('validates email presence', function (): void {
    $response = $this->json('POST', route('magic.link.store'), [
        'email' => '',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('validates email format', function (): void {
    $response = $this->json('POST', route('magic.link.store'), [
        'email' => 'not-an-email',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});
