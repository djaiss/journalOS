<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Instance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class InstanceDestroyAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function instance_administrator_can_destroy_an_account(): void
    {
        $user = User::factory()->create([
            'is_instance_admin' => true,
        ]);
        $account = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/instance/users/' . $account->id)
            ->delete('/instance/users/' . $account->id);

        $response->assertRedirect('/instance');

        $this->assertDatabaseMissing('users', [
            'id' => $account->id,
        ]);
    }

    #[Test]
    public function non_instance_administrator_cannot_destroy_an_account(): void
    {
        $user = User::factory()->create([
            'is_instance_admin' => false,
        ]);
        $account = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete('/instance/users/' . $account->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_destroy_an_account(): void
    {
        $account = User::factory()->create();

        $response = $this->delete('/instance/users/' . $account->id);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
        ]);
    }
}
