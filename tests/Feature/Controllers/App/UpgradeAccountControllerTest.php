<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UpgradeAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_upgrade_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/upgrade');

        $response->assertStatus(200);
        $response->assertSee('Upgrade your account');
    }
}
