<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ClaimAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_user_can_view_claim_page(): void
    {
        $user = User::factory()->create([
            'is_guest' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get('/claim');

        $response->assertStatus(200);
        $response->assertSee('Claim your account');
    }

    #[Test]
    public function non_guest_user_is_redirected_from_claim_page(): void
    {
        $user = User::factory()->create([
            'is_guest' => false,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get('/claim');

        $response->assertRedirect('/journals');
    }

    #[Test]
    public function guest_user_can_claim_account_and_is_redirected(): void
    {
        $user = User::factory()->create([
            'is_guest' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post('/claim', [
                'first_name' => 'Michael',
                'last_name' => 'Scott',
                'email' => 'michael.scott@dundermifflin.com',
                'password' => '5UTHSmdj',
                'password_confirmation' => '5UTHSmdj',
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('journal.index', absolute: false));

        $user->refresh();
        $this->assertFalse($user->is_guest);
        $this->assertSame('michael.scott@dundermifflin.com', $user->email);
        $this->assertNull($user->guest_token);
        $this->assertNull($user->guest_expires_at);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->trial_ends_at->isFuture());
    }

    #[Test]
    public function claim_validation_errors_do_not_update_user(): void
    {
        $user = User::factory()->create([
            'is_guest' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post('/claim', [
                'last_name' => 'Scott',
                'email' => 'michael.scott@dundermifflin.com',
                'password' => '5UTHSmdj',
                'password_confirmation' => '5UTHSmdj',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['first_name']);

        $user->refresh();
        $this->assertTrue($user->is_guest);
        $this->assertNotNull($user->email_verified_at);
    }
}
