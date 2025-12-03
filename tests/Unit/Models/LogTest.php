<?php

declare(strict_types=1);

use App\Models\Log;
use App\Models\User;

it('belongs to an organization', function (): void {
    $log = Log::factory()->create();

    expect($log->organization()->exists())->toBeTrue();
});

it('belongs to a user', function (): void {
    $log = Log::factory()->create();

    expect($log->user()->exists())->toBeTrue();
});

it('gets the name of the user', function (): void {
    $user = User::factory()->create([
        'first_name' => 'Dwight',
        'last_name' => 'Schrute',
        'nickname' => null,
    ]);
    $log = Log::factory()->create([
        'user_id' => $user->id,
        'user_name' => 'Jim Halpert',
    ]);

    expect($log->getUserName())->toEqual('Dwight Schrute');

    $log->user_id = null;
    $log->save();

    expect($log->refresh()->getUserName())->toEqual('Jim Halpert');
});
