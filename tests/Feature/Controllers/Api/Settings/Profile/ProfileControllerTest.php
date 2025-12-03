<?php

declare(strict_types=1);

use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

$userJsonStructure = [
    'data' => [
        'type',
        'id',
        'attributes' => [
            'first_name',
            'last_name',
            'nickname',
            'email',
            'locale',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ],
];

it('can get the current user profile', function () use ($userJsonStructure): void {
    Carbon::setTestNow('2025-07-01 00:00:00');
    $user = User::factory()->create([
        'first_name' => 'Michael',
        'last_name' => 'Scott',
        'email' => 'michael.scott@dundermifflin.com',
        'nickname' => 'Mike',
        'locale' => 'en',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/me');

    $response->assertStatus(200);
    $response->assertJsonStructure($userJsonStructure);

    $response->assertJson([
        'data' => [
            'type' => 'user',
            'id' => (string) $user->id,
            'attributes' => [
                'first_name' => 'Michael',
                'last_name' => 'Scott',
                'nickname' => 'Mike',
                'email' => 'michael.scott@dundermifflin.com',
                'locale' => 'en',
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp,
            ],
        ],
    ]);
});

it('can update the user profile', function () use ($userJsonStructure): void {
    Carbon::setTestNow('2025-07-01 00:00:00');
    $user = User::factory()->create([
        'first_name' => 'Michael',
        'last_name' => 'Scott',
        'email' => 'michael.scott@dundermifflin.com',
        'nickname' => 'Mike',
        'locale' => 'en',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/me', [
        'first_name' => 'Dwight',
        'last_name' => 'Schrute',
        'email' => 'dwight.schrute@dundermifflin.com',
        'nickname' => 'Dwight',
        'locale' => 'fr',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure($userJsonStructure);

    $response->assertJson([
        'data' => [
            'type' => 'user',
            'id' => (string) $user->id,
            'attributes' => [
                'first_name' => 'Dwight',
                'last_name' => 'Schrute',
                'nickname' => 'Dwight',
                'email' => 'dwight.schrute@dundermifflin.com',
                'locale' => 'fr',
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp,
            ],
        ],
    ]);
});
