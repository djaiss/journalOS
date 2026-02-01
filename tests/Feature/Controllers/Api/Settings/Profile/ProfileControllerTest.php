<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Settings\Profile;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ProfileControllerTest extends TestCase
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
                'time_format_24h',
                'created_at',
                'updated_at',
            ],
            'links' => [
                'self',
            ],
        ],
    ];

    #[Test]
    public function it_can_get_the_current_user_profile(): void
    {
        Carbon::setTestNow('2025-07-01 00:00:00');
        $user = User::factory()->create([
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'email' => 'michael.scott@dundermifflin.com',
            'nickname' => 'Mike',
            'locale' => 'en',
            'time_format_24h' => true,
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
                    'time_format_24h' => true,
                    'created_at' => Carbon::now()->timestamp,
                    'updated_at' => Carbon::now()->timestamp,
                ],
            ],
        ]);
    }

    #[Test]
    public function it_can_update_the_user_profile(): void
    {
        Carbon::setTestNow('2025-07-01 00:00:00');
        $user = User::factory()->create([
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'email' => 'michael.scott@dundermifflin.com',
            'nickname' => 'Mike',
            'locale' => 'en',
            'time_format_24h' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/me', [
            'first_name' => 'Dwight',
            'last_name' => 'Schrute',
            'email' => 'dwight.schrute@dundermifflin.com',
            'nickname' => 'Dwight',
            'locale' => 'fr',
            'time_format_24h' => false,
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
                    'time_format_24h' => false,
                    'created_at' => Carbon::now()->timestamp,
                    'updated_at' => Carbon::now()->timestamp,
                ],
            ],
        ]);
    }

    #[Test]
    public function it_rejects_first_name_longer_than_255_characters(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'email' => 'michael.scott@dundermifflin.com',
            'nickname' => 'Mike',
            'locale' => 'en',
            'time_format_24h' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/me', [
            'first_name' => str_repeat('a', 256),
            'last_name' => 'Scott',
            'email' => 'michael.scott@dundermifflin.com',
            'nickname' => 'Mike',
            'locale' => 'en',
            'time_format_24h' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['first_name']);
    }
}
