<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_in_a_user(): void
    {
        User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'michael.scott@dundermifflin.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'status',
            'data' => [
                'token',
            ],
        ]);

        $responseData = $response->json();
        $this->assertNotEmpty($responseData['data']['token']);
    }

    #[Test]
    public function it_fails_to_authenticate_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'michael.scott@dundermifflin.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
            'status',
        ]);

        $responseData = $response->json();
        $this->assertEquals('Invalid credentials', $responseData['message']);
        $this->assertEquals(401, $responseData['status']);
    }

    #[Test]
    public function it_logs_out_a_user(): void
    {
        $user = User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
            'password' => bcrypt('password'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', '/api/logout');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'status',
        ]);

        $responseData = $response->json();
        $this->assertEquals('Logged out successfully', $responseData['message']);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
