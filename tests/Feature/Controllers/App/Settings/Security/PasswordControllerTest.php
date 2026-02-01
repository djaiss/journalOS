<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Settings\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_allows_the_user_to_update_their_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/settings/security')
            ->put('/settings/password', [
                'current_password' => 'password',
                'new_password' => 'new-password',
                'new_password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect('/settings/security');
        $response->assertSessionHas('status', 'Changes saved');

        $this->assertTrue(password_verify('new-password', $user->fresh()->password));
    }
}
