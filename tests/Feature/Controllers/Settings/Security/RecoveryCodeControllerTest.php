<?php

declare(strict_types=1);

use App\Models\User;

it('shows the recovery codes', function (): void {
    $user = User::factory()->create([
        'two_factor_recovery_codes' => ['code1', 'code2', 'code3'],
    ]);

    $response = $this->actingAs($user)
        ->get('/settings/security/recovery-codes');

    $response->assertOk();
});
