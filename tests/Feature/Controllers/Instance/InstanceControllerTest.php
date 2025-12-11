<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Instance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class InstanceControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function instance_can_be_accessed_by_instance_administrator(): void
    {
        $user = User::factory()->create([
            'is_instance_admin' => true,
        ]);

        $response = $this->actingAs($user)
            ->get('/instance');

        $response->assertStatus(200);
    }

    #[Test]
    public function instance_can_not_be_accessed_by_non_instance_administrator(): void
    {
        $user = User::factory()->create([
            'is_instance_admin' => false,
        ]);

        $response = $this->actingAs($user)
            ->get('/instance');

        $response->assertStatus(403);
    }

    #[Test]
    public function you_can_see_an_option_to_access_instance_in_the_menu_if_you_have_the_right_to(): void
    {
        $user = User::factory()->create([
            'is_instance_admin' => true,
        ]);

        $response = $this->actingAs($user)
            ->get('/instance');

        $response->assertSee('Instance administration');
    }
}
