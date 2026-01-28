<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_registration_screen(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_registers_a_new_organization(): void
    {
        config(['app.show_marketing_site' => false]);

        $response = $this->post('/register', [
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'email' => 'michael.scott@dundermifflin.com',
            'password' => '5UTHSmdj',
            'password_confirmation' => '5UTHSmdj',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('journal.index', absolute: false));
    }
}
