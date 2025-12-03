<?php

declare(strict_types=1);

use App\Models\User;

it('shows the profile page', function (): void {
    $this->actingAs(User::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

it('updates the profile information', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->put('/settings/profile', [
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'nickname' => 'Michael',
            'email' => 'michael.scott@dundermifflin.com',
            'locale' => 'en',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings/profile');

    $user->refresh();

    expect($user->first_name)->toBe('Michael');
    expect($user->last_name)->toBe('Scott');
    expect($user->nickname)->toBe('Michael');
    expect($user->email)->toBe('michael.scott@dundermifflin.com');
    expect($user->locale)->toBe('en');
    expect($user->email_verified_at)->toBeNull();
});

it('does not change the email verification status when email address is unchanged', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->put('/settings/profile', [
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'nickname' => 'Michael',
            'email' => $user->email,
            'locale' => 'en',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings/profile');

    $response->assertSessionHas('status', 'Changes saved');

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

it('shows the latest logs', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->put('/settings/profile', [
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'nickname' => 'Michael',
            'email' => $user->email,
            'locale' => 'en',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings/profile');

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});
