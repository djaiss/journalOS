<?php

declare(strict_types=1);

use App\Models\User;

it('allows the user to update their password', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/settings/security')
        ->put('/settings/password', [
            'current_password' => 'password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ]);

    $response->assertRedirect('/settings/security');
    $response->assertSessionHas('status', 'Changes saved');

    expect(password_verify('new-password', $user->fresh()->password))->toBeTrue();
});
