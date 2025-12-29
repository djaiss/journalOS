<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Settings\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AutoDeleteAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_auto_delete_account_setting_to_true(): void
    {
        $user = User::factory()->create([
            'auto_delete_account' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/settings/security/auto-delete-account', [
            'auto_delete_account' => 'yes',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => trans('Changes saved'),
            'status' => 200,
        ]);

        $this->assertTrue($user->fresh()->auto_delete_account);
    }

    #[Test]
    public function it_updates_auto_delete_account_setting_to_false(): void
    {
        $user = User::factory()->create([
            'auto_delete_account' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/settings/security/auto-delete-account', [
            'auto_delete_account' => 'no',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => trans('Changes saved'),
            'status' => 200,
        ]);

        $this->assertFalse($user->fresh()->auto_delete_account);
    }
}
