<?php

declare(strict_types=1);

use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;

it('shows all the logs', function (): void {
    Carbon::setTestNow(Carbon::create(2018, 1, 1));
    $user = User::factory()->create([
        'first_name' => 'Ross',
        'last_name' => 'Geller',
        'nickname' => null,
    ]);

    $log = Log::factory()->create([
        'organization_id' => null,
        'user_id' => $user->id,
        'action' => 'profile_update',
        'description' => 'Updated their profile',
    ]);

    $response = $this->actingAs($user)
        ->get('/settings/profile/logs');

    $response->assertStatus(200);
    $response->assertViewIs('settings.logs.index');
    $response->assertViewHas('logs');

    $logs = $response->viewData('logs');

    expect($logs)->toHaveCount(1);
    expect($logs[0]->id)->toEqual($log->id);
    expect($logs[0]->user->getFullName())->toEqual('Ross Geller');
    expect($logs[0]->action)->toEqual('profile_update');
    expect($logs[0]->description)->toEqual('Updated their profile');
});

it('shows a pagination', function (): void {
    $user = User::factory()->create();

    Log::factory()->count(15)->create([
        'organization_id' => null,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->get('/settings/profile/logs');

    $response->assertStatus(200);
    expect($response['logs'])->toHaveCount(10);

    expect($response['logs']->hasMorePages())->toBeTrue();
});
