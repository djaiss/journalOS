<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

$collectionJsonStructure = [
    'data' => [
        '*' => [
            'type',
            'id',
            'attributes' => [
                'name',
                'slug',
                'avatar',
                'created_at',
                'updated_at',
            ],
            'links' => [
                'self',
            ],
        ],
    ],
];

$singleJsonStructure = [
    'data' => [
        'type',
        'id',
        'attributes' => [
            'name',
            'slug',
            'avatar',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ],
];

it('can list the organizations of the current user', function () use ($collectionJsonStructure): void {
    $user = User::factory()->create();

    $dunderMifflin = Organization::factory()->create(['name' => 'Dunder Mifflin']);
    $vancerefrigeration = Organization::factory()->create(['name' => 'Vance refrigeration']);

    $user->organizations()->attach($dunderMifflin->id, ['joined_at' => now()]);
    $user->organizations()->attach($vancerefrigeration->id, ['joined_at' => now()]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/organizations');

    $response->assertStatus(200);
    $response->assertJsonStructure($collectionJsonStructure);
    $response->assertJsonCount(2, 'data');

    $response->assertJson([
        'data' => [
            [
                'type' => 'organization',
                'id' => (string) $dunderMifflin->id,
                'attributes' => [
                    'name' => 'Dunder Mifflin',
                    'slug' => $dunderMifflin->slug,
                ],
            ],
            [
                'type' => 'organization',
                'id' => (string) $vancerefrigeration->id,
                'attributes' => [
                    'name' => 'Vance refrigeration',
                    'slug' => $vancerefrigeration->slug,
                ],
            ],
        ],
    ]);
});

it('returns empty collection when user has no organizations', function () use ($collectionJsonStructure): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/organizations');

    $response->assertStatus(200);
    $response->assertJsonStructure($collectionJsonStructure);
    $response->assertJsonCount(0, 'data');
});

it('can create a new organization', function () use ($singleJsonStructure): void {
    Carbon::setTestNow('2025-01-01 00:00:00');
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/organizations', [
        'name' => 'Dunder Mifflin',
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure($singleJsonStructure);

    $this->assertDatabaseHas('organizations', [
        'name' => 'Dunder Mifflin',
    ]);

    $organization = Organization::where('name', 'Dunder Mifflin')->first();

    $response->assertJson([
        'data' => [
            'type' => 'organization',
            'id' => (string) $organization->id,
            'attributes' => [
                'name' => 'Dunder Mifflin',
                'slug' => $organization->slug,
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp,
            ],
        ],
    ]);
});

it('can show an organization', function () use ($singleJsonStructure): void {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    $user->organizations()->attach($organization->id, ['joined_at' => now()]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/organizations/' . $organization->id);

    $response->assertStatus(200);
    $response->assertJsonStructure($singleJsonStructure);
});

it('restricts access to an organization', function () use ($singleJsonStructure): void {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/organizations/' . $organization->id);

    $response->assertStatus(403);
});
