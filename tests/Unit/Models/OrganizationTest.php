<?php

declare(strict_types=1);

use App\Models\EmailSent;
use App\Models\Group;
use App\Models\JobDiscipline;
use App\Models\JobFamily;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;

it('belongs to many users', function (): void {
    $organization = Organization::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $organization->users()->attach($user1->id, [
        'joined_at' => now(),
    ]);
    $organization->users()->attach($user2->id, [
        'joined_at' => now(),
    ]);

    expect($organization->users)->toHaveCount(2);
    expect($organization->users->contains($user1))->toBeTrue();
    expect($organization->users->contains($user2))->toBeTrue();
});

it('has many emails sent', function (): void {
    $organization = Organization::factory()->create();
    EmailSent::factory()->count(2)->create([
        'organization_id' => $organization->id,
    ]);

    expect($organization->emailsSent()->exists())->toBeTrue();
});

it('gets the avatar', function (): void {
    $organization = Organization::factory()->create();

    expect($organization->getAvatar())->toBeString();
});
