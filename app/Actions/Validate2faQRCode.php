<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Str;
use PragmaRX\Google2FALaravel\Google2FA;
use InvalidArgumentException;

/**
 * Validate the code from the QR code for 2FA setup.
 */
final readonly class Validate2faQRCode
{
    public function __construct(
        private User $user,
        private string $token,
        private ?Google2FA $google2fa = null,
    ) {}

    public function execute(): void
    {
        $this->validateToken();
        $this->generateRecoveryCodes();
    }

    private function validateToken(): void
    {
        $google2fa = $this->google2fa ?? new Google2FA(request());

        if (!$google2fa->verifyKey($this->user->two_factor_secret, $this->token)) {
            throw new InvalidArgumentException(__('The provided token is invalid.'));
        }

        $this->user->update(['two_factor_confirmed_at' => now()]);
    }

    private function generateRecoveryCodes(): void
    {
        $this->user->update(['two_factor_recovery_codes' => $this->generateRandomCodes()]);
    }

    private function generateRandomCodes(): array
    {
        return collect()->times(8)->map(fn() => Str::random(10))->all();
    }
}
