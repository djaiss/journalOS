<?php

declare(strict_types=1);

use App\Models\User;

it('lets user define the preferred two-factor authentication method', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/settings/security')
        ->put('/settings/security/2fa', [
            'preferred_method' => 'authenticator',
        ]);

    $response->assertRedirect('/settings/security');
    $response->assertSessionHas('status', trans('Changes saved'));

    expect($user->fresh()->two_factor_preferred_method)->toBe('authenticator');
});
