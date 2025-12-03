<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\UpdateUserInformation;
use App\Http\Controllers\Controller;
use App\Http\ViewModels\Settings\ProfileShowViewModel;
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
        $viewModel = new ProfileShowViewModel(
            user: $request->user(),
        );

        return view('settings.profile.index', [
            'user' => $request->user(),
            'viewModel' => $viewModel,
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
            'locale' => ['required', 'string', Rule::in(['en', 'fr'])],
        ]);

        new UpdateUserInformation(
            user: Auth::user(),
            email: $validated['email'],
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
            nickname: $validated['nickname'],
            locale: $validated['locale'],
        )->execute();

        return redirect()->route('settings.profile.index')
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
