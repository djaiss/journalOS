<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Settings\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ApiKeyControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_new_api_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/settings/security/create')
            ->post('/settings/api-keys', [
                'label' => 'My API Token',
            ]);

        $response->assertRedirect('/settings/security');
        $response->assertSessionHas('status', 'API key created');

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'My API Token',
            'tokenable_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_can_delete_an_api_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Test API Token');

        $response = $this->actingAs($user)
            ->delete('/settings/api-keys/' . $token->accessToken->id);

        $response->assertRedirect('/settings/security');
        $response->assertSessionHas('status', 'API key deleted');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }
}
