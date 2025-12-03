<?php

declare(strict_types=1);

use App\Models\EmailSent;
use App\Models\Organization;
use App\Models\User;

it('belongs to an organization', function (): void {
    $organization = Organization::factory()->create();
    $emailSent = EmailSent::factory()->create([
        'organization_id' => $organization->id,
    ]);

    expect($emailSent->organization()->exists())->toBeTrue();
});

it('belongs to a user', function (): void {
    $user = User::factory()->create();
    $emailSent = EmailSent::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($emailSent->user()->exists())->toBeTrue();
});
