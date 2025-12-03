<?php

declare(strict_types=1);

use App\Jobs\LogUserAction;
use App\Models\Log;
use App\Models\User;

it('logs user action', function (): void {
    $user = User::factory()->create([
        'first_name' => 'Michael',
        'last_name' => 'Scott',
        'nickname' => null,
    ]);
    LogUserAction::dispatch(
        organization: null,
        user: $user,
        action: 'personal_profile_update',
        description: 'Updated their personal profile',
    );

    $log = Log::first();

    expect($log->getUserName())->toEqual('Michael Scott');
    expect($log->action)->toEqual('personal_profile_update');
    expect($log->description)->toEqual('Updated their personal profile');
});
