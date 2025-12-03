<?php

declare(strict_types=1);


it('creates an account', function (): void {
    $page = visit('/register');

    $page->type('first_name', 'Michael');
    $page->type('last_name', 'Scott');
    $page->type('email', 'michael.scott@dundermifflin.com');
    $page->type('password', 'v@pfzY*9s');
    $page->type('password_confirmation', 'v@pfzY*9s');
    $page->press('@register-button');
    $page->assertPathIs('/verify-email');
});
