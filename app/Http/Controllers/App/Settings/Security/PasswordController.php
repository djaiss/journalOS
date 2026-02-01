<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Settings\Security;

use App\Actions\UpdateUserPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

final class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', 'max:255'],
            'new_password' => [
                'required',
                'string',
                'max:255',
                'confirmed',
                Password::min(8)->uncompromised(),
            ],
        ]);

        new UpdateUserPassword(
            user: Auth::user(),
            currentPassword: $validated['current_password'],
            newPassword: $validated['new_password'],
        )->execute();

        return to_route('settings.security.index')
            ->with('status', __('Changes saved'));
    }
}
