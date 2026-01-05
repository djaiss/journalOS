<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Auth;

use App\Enums\EmailType;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_login_screen(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_authenticates_a_user(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('journal.index', absolute: false));
    }

    #[Test]
    public function it_does_not_authenticate_a_user_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    #[Test]
    public function it_rejects_overlong_login_email(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => str_repeat('a', 250) . '@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function it_sends_an_email_on_failed_login(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        Queue::assertPushed(SendEmail::class, function (SendEmail $job) use ($user): bool {
            return $job->emailType === EmailType::LOGIN_FAILED && $job->user->id === $user->id;
        });
    }

    #[Test]
    public function it_logs_out_a_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
