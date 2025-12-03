<?php

declare(strict_types=1);

use App\Models\User;

test('login screen can be rendered', function (): void {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function (): void {
    $user = User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
        'password' => Illuminate\Support\Facades\Hash::make('5UTHSmdj'),
    ]);

    $response = $this->post('/login', [
        'email' => 'michael.scott@dundermifflin.com',
        'password' => '5UTHSmdj',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('organization.index', absolute: false));
});

test('users can not authenticate with invalid password', function (): void {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
