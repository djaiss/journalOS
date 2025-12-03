<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_the_registration_screen(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_it_registers_a_new_organization(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'email' => 'michael.scott@dundermifflin.com',
            'password' => '5UTHSmdj',
            'password_confirmation' => '5UTHSmdj',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('organization.index', absolute: false));
    }
}
