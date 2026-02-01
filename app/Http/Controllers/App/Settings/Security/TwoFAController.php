<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Settings\Security;

use App\Actions\Generate2faQRCode;
use App\Actions\Validate2faQRCode;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use InvalidArgumentException;

final class TwoFAController extends Controller
{
    public function create(): View
    {
        $code = new Generate2faQRCode(
            user: Auth::user(),
        )->execute();

        return view('app.settings.security.partials.2fa.new', [
            'secret' => $code['secret'],
            'qrCodeSvg' => $code['qrCodeSvg'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'numeric', 'digits:6'],
        ]);

        try {
            new Validate2faQRCode(
                user: Auth::user(),
                token: TextSanitizer::plainText((string) $request->input('token')),
            )->execute();
        } catch (InvalidArgumentException) {
            return back()
                ->withErrors(['token' => __('The provided token is invalid.')])
                ->withInput();
        }

        return to_route('settings.security.index')
            ->with('status', __('Two-factor authentication has been enabled successfully.'));
    }
}
