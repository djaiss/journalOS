<?php

declare(strict_types=1);

it('renders the registration screen', function (): void {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

it('registers a new organization', function (): void {
    $response = $this->post('/register', [
        'first_name' => 'Michael',
        'last_name' => 'Scott',
        'email' => 'michael.scott@dundermifflin.com',
        'password' => '5UTHSmdj',
        'password_confirmation' => '5UTHSmdj',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('organization.index', absolute: false));
});
