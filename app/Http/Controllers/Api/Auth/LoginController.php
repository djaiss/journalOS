<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LoginController extends Controller
{
    use ApiResponses;

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
        ]);

        if (! Auth::attempt($validated)) {
            return $this->error('Invalid credentials', 401);
        }

        $user = Auth::user();

        $tokenName = 'API token for ' . $user->email;

        $token = $user->createToken($tokenName)->plainTextToken;

        return $this->success('Authenticated', 200, [
            'token' => $token,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success('Logged out successfully', 200);
    }
}
