<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\UpdateUserInformation;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\EmailSent;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $logs = Log::query()
            ->where('user_id', $request->user()->id)
            ->with('user')
            ->with('journal')
            ->latest()
            ->limit(6)
            ->get();

        $emails = EmailSent::query()
            ->where('user_id', $request->user()->id)
            ->with('user')
            ->latest('sent_at')
            ->limit(6)
            ->get();

        return view('app.settings.profile.index', [
            'user' => $request->user(),
            'logs' => $logs,
            'emails' => $emails,
            'hasMoreLogs' => $logs->count() > 5,
            'hasMoreEmails' => $emails->count() > 5,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(Auth::user()->id),
            ],
            'locale' => ['required', 'string', 'max:255', Rule::in(['en', 'fr'])],
            'time_format_24h' => ['required', Rule::in(['true', 'false'])],
        ]);

        new UpdateUserInformation(
            user: Auth::user(),
            email: mb_strtolower(TextSanitizer::plainText($validated['email'])),
            firstName: TextSanitizer::plainText($validated['first_name']),
            lastName: TextSanitizer::plainText($validated['last_name']),
            nickname: TextSanitizer::nullablePlainText($validated['nickname']),
            locale: TextSanitizer::plainText($validated['locale']),
            timeFormat24h: $validated['time_format_24h'] === 'true' ? true : false,
        )->execute();

        return to_route('settings.profile.index')
            ->with('status', __('Changes saved'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}
