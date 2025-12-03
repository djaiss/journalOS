<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Security;

use App\Actions\Generate2faQRCode;
use App\Actions\Validate2faQRCode;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Http\RedirectResponse;

final class TwoFAController extends Controller
{
    public function create(): View
    {
        $code = new Generate2faQRCode(
            user: Auth::user(),
        )->execute();

        return view('settings.security.partials.2fa.new', [
            'secret' => $code['secret'],
            'qrCodeSvg' => $code['qrCodeSvg'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required|numeric|digits:6',
        ]);

        try {
            new Validate2faQRCode(
                user: Auth::user(),
                token: (string) $request->input('token'),
            )->execute();
        } catch (InvalidArgumentException) {
            return back()
                ->withErrors(['token' => __('The provided token is invalid.')])
                ->withInput();
        }

        return redirect()->route('settings.security.index')
            ->with('status', __('Two-factor authentication has been enabled successfully.'));
    }
}
