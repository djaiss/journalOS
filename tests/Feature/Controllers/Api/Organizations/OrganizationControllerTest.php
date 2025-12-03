<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Organizations;

use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $collectionJsonStructure = [
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

    private array $singleJsonStructure = [
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

    #[Test]
    public function it_can_list_the_organizations_of_the_current_user(): void
    {
        $user = User::factory()->create();

        $dunderMifflin = Organization::factory()->create(['name' => 'Dunder Mifflin']);
        $vancerefrigeration = Organization::factory()->create(['name' => 'Vance refrigeration']);

        $user->organizations()->attach($dunderMifflin->id, ['joined_at' => now()]);
        $user->organizations()->attach($vancerefrigeration->id, ['joined_at' => now()]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/organizations');

        $response->assertStatus(200);
        $response->assertJsonStructure($this->collectionJsonStructure);
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
    }

    #[Test]
    public function it_returns_empty_collection_when_user_has_no_organizations(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/organizations');

        $response->assertStatus(200);
        $response->assertJsonStructure($this->collectionJsonStructure);
        $response->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_can_create_a_new_organization(): void
    {
        Carbon::setTestNow('2025-01-01 00:00:00');
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/organizations', [
            'name' => 'Dunder Mifflin',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure($this->singleJsonStructure);

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
    }

    #[Test]
    public function it_can_show_an_organization(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $user->organizations()->attach($organization->id, ['joined_at' => now()]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/organizations/' . $organization->id);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->singleJsonStructure);
    }

    #[Test]
    public function it_restricts_access_to_an_organization(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/organizations/' . $organization->id);

        $response->assertStatus(403);
    }
}
