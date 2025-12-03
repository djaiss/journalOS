<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Settings\Security;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ApiKeyControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $collectionJsonStructure = [
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

    private array $singleJsonStructure = [
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

    #[Test]
    public function it_can_list_the_api_keys_of_the_current_user(): void
    {
        Carbon::setTestNow('2025-07-01 00:00:00');
        $user = User::factory()->create();

        $token1 = $user->createToken('Test API Key 1');
        $token2AccessToken = $user->createToken('Test API Key 2')->accessToken;
        $token2AccessToken->last_used_at = Carbon::now()->subDays(5);
        $token2AccessToken->save();

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/settings/api');

        $response->assertJsonStructure($this->collectionJsonStructure);

        $response->assertJsonCount(2, 'data');
    }

    #[Test]
    public function it_can_create_a_new_api_key(): void
    {
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

        $response->assertJsonStructure($this->singleJsonStructure);
    }

    #[Test]
    public function user_can_delete_their_api_key(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Test API Key');
        $tokenId = $token->accessToken->id;

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', "/api/settings/api/{$tokenId}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    #[Test]
    public function it_can_get_a_single_api_key(): void
    {
        Carbon::setTestNow('2025-07-01 00:00:00');
        $user = User::factory()->create();
        $token = $user->createToken('Test API Key');
        $tokenId = $token->accessToken->id;

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/settings/api/{$tokenId}");

        $response->assertStatus(200);
        $response->assertJsonStructure($this->singleJsonStructure);

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
    }

    #[Test]
    public function it_returns_404_when_api_key_not_found(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/settings/api/999');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'API key not found',
            'status' => 404,
        ]);
    }
}
