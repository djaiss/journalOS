<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Instance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class InstanceFreeAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function instance_administrator_can_give_free_account(): void
    {
        $user = User::factory()->create([
            'is_instance_admin' => true,
        ]);
        $account = User::factory()->create([
            'has_lifetime_access' => false,
        ]);

        $response = $this->actingAs($user)
            ->from('/instance/users/' . $account->id)
            ->put('/instance/users/' . $account->id . '/free');

        $response->assertRedirect('/instance/users/' . $account->id);

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'has_lifetime_access' => true,
        ]);
    }

    #[Test]
    public function non_instance_administrator_cannot_give_free_account(): void
    {
        $user = User::factory()->create([
            'is_instance_admin' => false,
        ]);
        $account = User::factory()->create([
            'has_lifetime_access' => false,
        ]);

        $response = $this->actingAs($user)
            ->put('/instance/users/' . $account->id . '/free');

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'has_lifetime_access' => false,
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_give_free_account(): void
    {
        $account = User::factory()->create([
            'has_lifetime_access' => false,
        ]);

        $response = $this->put('/instance/users/' . $account->id . '/free');
        $response->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'has_lifetime_access' => false,
        ]);
    }
}
