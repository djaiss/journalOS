<?php

declare(strict_types=1);

use App\Models\User;
use App\Actions\CreateMagicLink;
use Illuminate\Database\Eloquent\ModelNotFoundException;

it('returns a string', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $magicLinkUrl = (new CreateMagicLink(
        email: $user->email,
    ))->execute();

    expect($magicLinkUrl)->toBeString();
});

it('contains the app url with magic link structure', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $magicLinkUrl = (new CreateMagicLink(
        email: $user->email,
    ))->execute();

    $appUrl = config('app.url');
    expect($magicLinkUrl)->toStartWith($appUrl . '/magiclink/');
    expect($magicLinkUrl)->toMatch('/\/magiclink\/[a-f0-9-]+%3A[A-Za-z0-9]+/');
});

it('throws an exception if user not found', function (): void {
    $nonExistentEmail = 'nonexistent@example.com';

    $this->expectException(ModelNotFoundException::class);

    (new CreateMagicLink(
        email: $nonExistentEmail,
    ))->execute();
});
