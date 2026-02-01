<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

/**
 * Service to verify a user's 2FA code (TOTP or rescue code).
 */
final readonly class VerifyTwoFactorCode
{
    public function __construct(
        private User $user,
        private string $code,
    ) {}

    /**
     * Execute the verification of the 2FA code.
     *
     * @return bool True if the code is valid, false otherwise
     */
    public function execute(): bool
    {
        if ($this->verifyTotp()) {
            $this->updateUserLastActivityDate();

            return true;
        }

        if ($this->verifyRescueCode()) {
            $this->updateUserLastActivityDate();

            return true;
        }

        return false;
    }

    private function verifyTotp(): bool
    {
        if (!$this->user->two_factor_secret) {
            return false;
        }

        $secret = $this->user->two_factor_secret;
        $google2fa = new Google2FA;

        return $google2fa->verifyKey($secret, $this->code);
    }

    private function verifyRescueCode(): bool
    {
        if (!is_array($this->user->two_factor_recovery_codes)) {
            return false;
        }

        $codes = $this->user->two_factor_recovery_codes;
        if (in_array($this->code, $codes, true)) {
            $this->user->two_factor_recovery_codes = array_values(array_diff($codes, [$this->code]));
            $this->user->save();

            return true;
        }

        return false;
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
