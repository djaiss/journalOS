<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_render_the_reset_password_link_screen(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_can_request_a_reset_password_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    #[Test]
    public function it_can_render_the_reset_password_screen(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/' . $notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    #[Test]
    public function it_can_reset_the_password_with_a_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => '!pQrcqwak',
                'password_confirmation' => '!pQrcqwak',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
