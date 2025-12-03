<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Security;

use App\Actions\CreateApiKey;
use App\Actions\DestroyApiKey;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ApiKeyController extends Controller
{
    public function create(): View
    {
        return view('settings.security.partials.api.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|min:3|max:255',
        ]);

        $apiKey = new CreateApiKey(
            user: Auth::user(),
            label: $validated['label'],
        )->execute();

        return redirect()->route('settings.security.index')
            ->with('apiKey', $apiKey)
            ->with('status', trans('API key created'));
    }

    public function destroy(Request $request, int $apiKeyId): RedirectResponse
    {
        $apiKey = Auth::user()->tokens()->where('id', $apiKeyId)->first();

        new DestroyApiKey(
            user: Auth::user(),
            tokenId: $apiKey->id,
        )->execute();

        return redirect()->route('settings.security.index')
            ->with('status', trans('API key deleted'));
    }
}
