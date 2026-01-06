<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Auth;

use App\Actions\CreateAccount;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Jobs\CheckLastLogin;
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
        return view('app.auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class, 'disposable_email'],
            'password' => [
                'required',
                'string',
                'max:255',
                'confirmed',
                Password::min(8)->uncompromised(),
            ],
        ]);

        $user = new CreateAccount(
            email: mb_strtolower(TextSanitizer::plainText((string) $request->input('email'))),
            password: $request->input('password'),
            firstName: TextSanitizer::plainText((string) $request->input('first_name')),
            lastName: TextSanitizer::plainText((string) $request->input('last_name')),
        )->execute();

        event(new Registered($user));

        Auth::login($user);

        CheckLastLogin::dispatch(user: Auth::user(), ip: $request->ip())->onQueue('low');

        return redirect(route('journal.index', absolute: false));
    }
}
