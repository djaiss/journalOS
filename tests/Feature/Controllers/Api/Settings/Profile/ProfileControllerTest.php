<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Settings\Profile;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $userJsonStructure = [
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

    public function test_it_can_get_the_current_user_profile(): void
    {
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
        $response->assertJsonStructure($this->userJsonStructure);

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
    }

    public function test_it_can_update_the_user_profile(): void
    {
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
        $response->assertJsonStructure($this->userJsonStructure);

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
    }
}
