<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Auth;

use App\Actions\VerifyTwoFactorCode;
use App\Enums\EmailType;
use App\Enums\TwoFactorType;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Jobs\CheckLastLogin;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use NjoguAmos\Turnstile\Rules\TurnstileRule;

final class LoginController extends Controller
{
    public function create(): View
    {
        $quotes = config('quotes');
        $randomQuote = $quotes[array_rand($quotes)];

        return view('app.auth.login', [
            'quote' => $randomQuote,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (config('app.show_marketing_site')) {
            $request->validate([
                'cf-turnstile-response' => ['required', new TurnstileRule],
            ]);
        }

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $this->ensureIsNotRateLimited($request);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request));

            $user = User::query()->where('email', $request->input('email'))->first();

            if ($user) {
                SendEmail::dispatch(
                    emailType: EmailType::LOGIN_FAILED,
                    user: $user,
                    parameters: [],
                )->onQueue('high');
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if (Auth::user()->two_factor_preferred_method === TwoFactorType::AUTHENTICATOR->value) {
            $userId = Auth::user()->id; // Retrieve the user's ID before logging out
            Auth::logout();
            session(['2fa:user:id' => $userId]); // Use the stored ID to set the session value

            return to_route('2fa.challenge');
        }

        RateLimiter::clear($this->throttleKey($request));
        CheckLastLogin::dispatch(user: Auth::user(), ip: $request->ip())->onQueue('low');

        $request->session()->regenerate();

        return redirect()->intended(route('journal.index', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->string('email')) . '|' . $request->ip());
    }

    /**
     * Display the 2FA challenge form if required.
     *
     * @return View
     */
    public function show2faForm(): View
    {
        if (!session('2fa:user:id')) {
            return view('app.auth.2fa', [
                'error' => __('Session expired. Please login again.'),
            ]);
        }

        return view('app.auth.2fa');
    }

    /**
     * Verify the 2FA code and complete login.
     *
     * @return RedirectResponse
     */
    public function verify2fa(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        $userId = session('2fa:user:id');
        $user = User::query()->find($userId);

        if (!new VerifyTwoFactorCode(
            user: $user,
            code: TextSanitizer::plainText((string) $request->input('code')),
        )->execute()) {
            return back()->withErrors(['code' => 'Invalid code']);
        }

        Auth::login($user);
        session()->forget('2fa:user:id');
        $request->session()->regenerate();

        CheckLastLogin::dispatch(user: Auth::user(), ip: $request->ip())->onQueue('low');

        return redirect()->intended(route('journal.index', absolute: false));
    }
}
