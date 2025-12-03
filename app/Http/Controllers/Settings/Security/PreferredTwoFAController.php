<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Security;

use App\Http\Controllers\Controller;
use App\Actions\UpdateTwoFAMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class PreferredTwoFAController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'preferred_method' => 'required|string|in:none,authenticator,email',
        ]);

        new UpdateTwoFAMethod(
            user: Auth::user(),
            preferredMethods: $validated['preferred_method'],
        )->execute();

        return redirect()->route('settings.security.index')
            ->with('status', trans('Changes saved'));
    }
}
