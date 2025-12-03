<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Security;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class RecoveryCodeController extends Controller
{
    public function show(): View
    {
        $recoveryCodes = Auth::user()->two_factor_recovery_codes ?? [];

        return view('settings.security.partials.2fa.recovery-codes', [
            'recoveryCodes' => collect($recoveryCodes),
        ]);
    }
}
