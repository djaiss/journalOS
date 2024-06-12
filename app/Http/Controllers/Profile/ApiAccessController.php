<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\ViewModels\Profile\ApiAccessViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ApiAccessController extends Controller
{
    public function index(Request $request): View
    {
        if ($request->header('hx-request') && $request->header('hx-target') == 'tokens-index') {
            return view('settings.api.partials.tokens-list', [
                'data' => ApiAccessViewModel::index(),
            ]);
        }

        return view('settings.api.index', [
            'data' => ApiAccessViewModel::index(),
        ]);
    }

    public function new(): View
    {
        return view('settings.api.new');
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token_name' => 'required|string|max:255',
        ]);

        $token = $request->user()->createToken($validated['token_name']);

        $request->session()->flash('status', __('The key has been created'));

        return Redirect::route('settings.api.index')->with('key', $token->plainTextToken);
    }

    public function destroy(Request $request, int $id): Response
    {
        auth()->user()->tokens()->where('id', $id)->delete();

        return response()->make('', 200, ['HX-Trigger' => 'loadTokens']);
    }
}
