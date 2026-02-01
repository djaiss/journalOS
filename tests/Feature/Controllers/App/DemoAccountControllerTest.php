<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DemoAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_user_can_create_guest_account(): void
    {
        $this->assertCount(0, User::all());

        $response = $this->get('/demo');

        $response->assertRedirect('/journals');
        $this->assertCount(1, User::all());

        $user = User::first();
        $this->assertTrue($user->is_guest);
        $this->assertNotNull($user->guest_token);
        $this->assertNotNull($user->guest_expires_at);
        $this->assertTrue($user->email_verified_at !== null);
    }

    #[Test]
    public function authenticated_user_is_redirected_to_journals(): void
    {
        $user = User::factory()->create([
            'is_guest' => false,
        ]);

        $response = $this->actingAs($user)
            ->get('/demo');

        $response->assertRedirect('/journals');
        $this->assertCount(1, User::all());
    }

    #[Test]
    public function guest_user_is_authenticated_after_creation(): void
    {
        $this->assertFalse(auth()->check());

        $this->get('/demo');

        $this->assertTrue(auth()->check());
        $this->assertTrue(auth()->user()->is_guest);
    }

    #[Test]
    public function guest_account_is_created_with_trial_period(): void
    {
        $this->get('/demo');

        $user = User::first();
        $this->assertNotNull($user->trial_ends_at);
        $this->assertTrue($user->trial_ends_at->isFuture());
    }
}
