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
use NjoguAmos\Turnstile\Rules\TurnstileRule;

final class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('app.auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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

        if (config('journalos.show_marketing_site')) {
            $request->validate([
                'cf-turnstile-response' => ['required', new TurnstileRule()],
            ]);
        }

        $user = new CreateAccount(
            email: mb_strtolower(TextSanitizer::plainText((string) $validated['email'])),
            password: $validated['password'],
            firstName: TextSanitizer::plainText((string) $validated['first_name']),
            lastName: TextSanitizer::plainText((string) $validated['last_name']),
        )->execute();

        event(new Registered($user));

        Auth::login($user);

        CheckLastLogin::dispatch(user: Auth::user(), ip: $request->ip())->onQueue('low');

        return redirect(route('journal.index', absolute: false));
    }
}
