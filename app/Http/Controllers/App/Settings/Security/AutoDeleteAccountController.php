<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings\Security;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AutoDeleteAccountController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'auto_delete_account' => ['required', 'boolean'],
        ]);

        $user = User::query()->find(Auth::user()->id);

        $user->auto_delete_account = (bool) $validated['auto_delete_account'];
        $user->save();

        return to_route('settings.security.index')
            ->with('status', trans('Changes saved'));
    }
}
