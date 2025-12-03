<?php

declare(strict_types=1);

use App\Models\User;

it('deletes an API key', function (): void {
    $user = User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
    ]);
    $user->createToken('Test API Key');

    $this->actingAs($user);

    $page = visit('/settings/security');

    // Override the confirm function to always return true
    $page->script('window.confirm = function() { return true; }');

    $page->press('@delete-api-key-' . $user->tokens()->latest()->first()->id);
    $page->assertSee('API key deleted');
});
