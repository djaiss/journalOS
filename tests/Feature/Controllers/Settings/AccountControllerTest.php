<?php

declare(strict_types=1);

use App\Models\User;
use Carbon\Carbon;

it('deletes the user account', function (): void {
    Carbon::setTestNow(Carbon::create(2018, 1, 1));
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/settings/account');

    $response->assertStatus(200);
    $response->assertViewIs('settings.account.index');

    $response = $this->actingAs($user)
        ->delete('/settings/account', [
            'feedback' => 'I want to delete my account',
        ]);

    $response->assertRedirect('/login');
});
