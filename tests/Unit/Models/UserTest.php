<?php

declare(strict_types=1);

use App\Models\EmailSent;
use App\Models\User;
use App\Models\Organization;

it('belongs to many organizations', function (): void {
    $user = User::factory()->create();
    $organization1 = Organization::factory()->create();
    $organization2 = Organization::factory()->create();

    $user->organizations()->attach($organization1->id, [
        'joined_at' => now(),
    ]);
    $user->organizations()->attach($organization2->id, [
        'joined_at' => now(),
    ]);

    expect($user->organizations)->toHaveCount(2);
    expect($user->organizations->contains($organization1))->toBeTrue();
    expect($user->organizations->contains($organization2))->toBeTrue();
});

it('has many emails sent', function (): void {
    $user = User::factory()->create();
    EmailSent::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->emailsSent()->exists())->toBeTrue();
});

it('gets the name', function (): void {
    $user = User::factory()->create([
        'first_name' => 'Dwight',
        'last_name' => 'Schrute',
        'nickname' => null,
    ]);

    expect($user->getFullName())->toEqual('Dwight Schrute');

    $user->nickname = 'The Beet Farmer';
    $user->save();
    expect($user->getFullName())->toEqual('The Beet Farmer');
});

it('has initials', function (): void {
    $dwight = User::factory()->create([
        'first_name' => 'Dwight',
        'last_name' => 'Schrute',
    ]);

    expect($dwight->initials())->toEqual('DS');
});

it('checks organization membership', function (): void {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    expect($user->isPartOfOrganization($organization))->toBeFalse();

    $user->organizations()->attach($organization->id, [
        'joined_at' => now(),
    ]);
    expect($user->isPartOfOrganization($organization))->toBeTrue();
});
