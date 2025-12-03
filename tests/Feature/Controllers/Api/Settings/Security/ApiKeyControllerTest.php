<?php

declare(strict_types=1);

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
                'token',
                'last_used_at',
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
            'token',
            'last_used_at',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ],
];

it('can list the api keys of the current user', function () use ($collectionJsonStructure): void {
    Carbon::setTestNow('2025-07-01 00:00:00');
    $user = User::factory()->create();

    $token1 = $user->createToken('Test API Key 1');
    $token2AccessToken = $user->createToken('Test API Key 2')->accessToken;
    $token2AccessToken->last_used_at = Carbon::now()->subDays(5);
    $token2AccessToken->save();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/settings/api');

    $response->assertJsonStructure($collectionJsonStructure);

    $response->assertJsonCount(2, 'data');
});

it('can create a new api key', function () use ($singleJsonStructure): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/settings/api', [
        'label' => 'New API Key',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('personal_access_tokens', [
        'name' => 'New API Key',
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
    ]);

    $response->assertJsonStructure($singleJsonStructure);
});

it('user can delete their api key', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('Test API Key');
    $tokenId = $token->accessToken->id;

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', "/api/settings/api/{$tokenId}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId,
    ]);
});

it('can get a single api key', function () use ($singleJsonStructure): void {
    Carbon::setTestNow('2025-07-01 00:00:00');
    $user = User::factory()->create();
    $token = $user->createToken('Test API Key');
    $tokenId = $token->accessToken->id;

    Sanctum::actingAs($user);

    $response = $this->json('GET', "/api/settings/api/{$tokenId}");

    $response->assertStatus(200);
    $response->assertJsonStructure($singleJsonStructure);

    $response->assertJson([
        'data' => [
            'type' => 'api_key',
            'id' => (string) $tokenId,
            'attributes' => [
                'name' => 'Test API Key',
                'token' => null,
                'last_used_at' => null,
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp,
            ],
        ],
    ]);
});

it('returns 404 when api key not found', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/settings/api/999');

    $response->assertStatus(404);
    $response->assertJson([
        'message' => 'API key not found',
        'status' => 404,
    ]);
});
