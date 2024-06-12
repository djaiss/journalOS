<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_the_information_about_the_logged_user(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create([
                'name' => 'Dwight Schrute',
                'email' => 'dwight.schrute@dundermifflin.com',
            ])
        );

        $response = $this->json('GET', '/api/me');

        $response->assertStatus(200);

        $this->assertEquals(
            $response->json(),
            [
                'id' => $user->id,
                'name' => 'Dwight Schrute',
                'email' => 'dwight.schrute@dundermifflin.com',
            ]
        );
    }
}
