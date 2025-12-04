<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\CreateAccount;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

final class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class, 'disposable_email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->uncompromised(),
            ],
        ]);

        $user = new CreateAccount(
            email: $request->input('email'),
            password: $request->input('password'),
            firstName: $request->input('first_name'),
            lastName: $request->input('last_name'),
        )->execute();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('journal.index', absolute: false));
    }
}
