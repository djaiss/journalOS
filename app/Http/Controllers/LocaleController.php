<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'max:255', Rule::in(config('journalos.supported_locales'))],
        ]);

        App::setLocale($validated['locale']);
        session()->put('locale', $validated['locale']);

        if (Auth::check()) {
            Auth::user()->update(['locale' => $validated['locale']]);
        }

        return back()
            ->with('status', __('Locale updated'));
    }
}
