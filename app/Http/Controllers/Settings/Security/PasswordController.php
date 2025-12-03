<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Security;

use App\Actions\UpdateUserPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

final class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)->uncompromised(),
            ],
        ]);

        new UpdateUserPassword(
            user: Auth::user(),
            currentPassword: $validated['current_password'],
            newPassword: $validated['new_password'],
        )->execute();

        return redirect()->route('settings.security.index')
            ->with('status', __('Changes saved'));
    }
}
