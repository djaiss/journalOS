<?php

declare(strict_types=1);

use App\Models\User;

it('logs in a user', function (): void {
    User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
    ]);

    $page = visit('/login');

    $page->type('email', 'michael.scott@dundermifflin.com');
    $page->type('password', 'password');
    $page->press('@login-button');
    $page->assertPathIs('/organizations');
});

it('logs in a user using a magic link', function (): void {
    User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
    ]);

    $page = visit('/login');
    $page->press('@magic-link-link');
    $page->type('email', 'michael.scott@dundermifflin.com');
    $page->press('@send-button');
    $page->assertPathIs('/send-magic-link');
});
