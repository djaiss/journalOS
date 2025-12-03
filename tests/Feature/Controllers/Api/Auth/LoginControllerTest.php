<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('logs in a user', function (): void {
    User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->json('POST', '/api/login', [
        'email' => 'michael.scott@dundermifflin.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'status',
        'data' => [
            'token',
        ],
    ]);

    $responseData = $response->json();
    expect($responseData['data']['token'])->not->toBeEmpty();
});

it('fails to authenticate with invalid credentials', function (): void {
    User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->json('POST', '/api/login', [
        'email' => 'michael.scott@dundermifflin.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
    $response->assertJsonStructure([
        'message',
        'status',
    ]);

    $responseData = $response->json();
    expect($responseData['message'])->toBe('Invalid credentials');
    expect($responseData['status'])->toBe(401);
});

it('logs out a user', function (): void {
    $user = User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
        'password' => bcrypt('password'),
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', '/api/logout');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'status',
    ]);

    $responseData = $response->json();
    expect($responseData['message'])->toBe('Logged out successfully');

    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});
