<?php

declare(strict_types=1);

use App\Actions\Validate2faQRCode;
use App\Models\User;
use PragmaRX\Google2FALaravel\Google2FA;

it('validates the 2fa QR code and generates recovery codes', function (): void {
    $secret = 'JBSWY3DPEHPK3PXP';

    $user = User::factory()->create([
        'two_factor_secret' => $secret,
        'two_factor_confirmed_at' => null,
    ]);

    $google2faMock = Mockery::mock(Google2FA::class);
    $google2faMock->shouldReceive('verifyKey')
        ->once()
        ->with($secret, '123456')
        ->andReturn(true);

    (new Validate2faQRCode(
        user: $user,
        token: '123456',
        google2fa: $google2faMock,
    ))->execute();

    $user->refresh();

    expect($user->two_factor_confirmed_at)->not->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeArray();
    expect($user->two_factor_recovery_codes)->toHaveCount(8);

    foreach ($user->two_factor_recovery_codes as $code) {
        expect($code)->toBeString();
        expect(mb_strlen($code))->toBe(10);
    }
});

it('throws exception when token is invalid', function (): void {
    $secret = 'JBSWY3DPEHPK3PXP';

    $user = User::factory()->create([
        'two_factor_secret' => $secret,
        'two_factor_confirmed_at' => null,
    ]);

    $google2faMock = Mockery::mock(Google2FA::class);
    $google2faMock->shouldReceive('verifyKey')
        ->once()
        ->with($secret, 'wrong-token')
        ->andReturn(false);

    expect(fn() => (new Validate2faQRCode(
        user: $user,
        token: 'wrong-token',
        google2fa: $google2faMock,
    ))->execute())->toThrow(InvalidArgumentException::class, 'The provided token is invalid.');
});

it('does not update recovery codes when token is invalid', function (): void {
    $secret = 'JBSWY3DPEHPK3PXP';

    $user = User::factory()->create([
        'two_factor_secret' => $secret,
        'two_factor_confirmed_at' => null,
        'two_factor_recovery_codes' => null,
    ]);

    $google2faMock = Mockery::mock(Google2FA::class);
    $google2faMock->shouldReceive('verifyKey')
        ->once()
        ->with($secret, 'invalid-token')
        ->andReturn(false);

    try {
        (new Validate2faQRCode(
            user: $user,
            token: 'invalid-token',
            google2fa: $google2faMock,
        ))->execute();
    } catch (InvalidArgumentException $e) {
        // Expected exception
    }

    $user->refresh();

    expect($user->two_factor_confirmed_at)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
});
