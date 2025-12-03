<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Actions\DestroyAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class AccountController extends Controller
{
    public function index(): View
    {
        return view('settings.account.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'feedback' => 'required|string|min:3|max:255',
        ]);

        new DestroyAccount(
            user: Auth::user(),
            reason: $validated['feedback'],
        )->execute();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
