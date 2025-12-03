<?php

declare(strict_types=1);

use App\Models\User;

it('displays the change password form', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/settings/security');

    $response->assertStatus(200);
    $response->assertViewIs('settings.security.index');
});
