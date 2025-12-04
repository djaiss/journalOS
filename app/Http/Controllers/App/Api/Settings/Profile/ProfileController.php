<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Api\Settings\Profile;

use App\Http\Controllers\Controller;
use App\Actions\UpdateUserInformation;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class ProfileController extends Controller
{
    use ApiResponses;

    /**
     * Get the information about the logged user.
     */
    public function show(): UserResource
    {
        return new UserResource(Auth::user());
    }

    /**
     * Update your profile.
     */
    public function update(Request $request): UserResource
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore(Auth::user()->id)],
            'nickname' => ['nullable', 'string', 'max:255'],
            'locale' => ['required', 'string', 'max:255'],
        ]);

        new UpdateUserInformation(
            user: Auth::user(),
            email: mb_strtolower((string) $validated['email']),
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
            nickname: $validated['nickname'] ?? null,
            locale: $validated['locale'],
        )->execute();

        return new UserResource(Auth::user()->refresh());
    }
}
