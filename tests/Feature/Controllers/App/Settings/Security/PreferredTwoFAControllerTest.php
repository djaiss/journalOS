<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Settings\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PreferredTwoFAControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lets_user_define_the_preferred_two_factor_authentication_method(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/settings/security')
            ->put('/settings/security/2fa', [
                'preferred_method' => 'authenticator',
            ]);

        $response->assertRedirect('/settings/security');
        $response->assertSessionHas('status', trans('Changes saved'));

        $this->assertEquals('authenticator', $user->fresh()->two_factor_preferred_method);
    }
}
