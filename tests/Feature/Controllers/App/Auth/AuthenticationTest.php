<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    #[Test]
    public function users_can_authenticate_using_the_login_screen(): void
    {
        config(['app.show_marketing_site' => false]);
        User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
            'password' => Hash::make('5UTHSmdj'),
        ]);

        $response = $this->post('/login', [
            'email' => 'michael.scott@dundermifflin.com',
            'password' => '5UTHSmdj',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('journal.index', absolute: false));
    }

    #[Test]
    public function users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    #[Test]
    public function users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
