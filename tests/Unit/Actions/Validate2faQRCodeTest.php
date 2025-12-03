<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\Validate2faQRCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Mockery;
use PragmaRX\Google2FALaravel\Google2FA;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Validate2faQRCodeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_validates_the_2fa_qr_code_and_generates_recovery_codes(): void
    {
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

        $this->assertNotNull($user->two_factor_confirmed_at);
        $this->assertIsArray($user->two_factor_recovery_codes);
        $this->assertCount(8, $user->two_factor_recovery_codes);

        foreach ($user->two_factor_recovery_codes as $code) {
            $this->assertIsString($code);
            $this->assertEquals(10, mb_strlen($code));
        }
    }

    #[Test]
    public function it_throws_exception_when_token_is_invalid(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The provided token is invalid.');

        (new Validate2faQRCode(
            user: $user,
            token: 'wrong-token',
            google2fa: $google2faMock,
        ))->execute();
    }

    #[Test]
    public function it_does_not_update_recovery_codes_when_token_is_invalid(): void
    {
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

        $this->assertNull($user->two_factor_confirmed_at);
        $this->assertNull($user->two_factor_recovery_codes);
    }
}
