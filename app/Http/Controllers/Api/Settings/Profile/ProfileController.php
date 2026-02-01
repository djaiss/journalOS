<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Settings\Profile;

use App\Actions\UpdateUserInformation;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
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
            'time_format_24h' => ['required', 'boolean'],
        ]);

        new UpdateUserInformation(
            user: Auth::user(),
            email: mb_strtolower(TextSanitizer::plainText($validated['email'])),
            firstName: TextSanitizer::plainText($validated['first_name']),
            lastName: TextSanitizer::plainText($validated['last_name']),
            nickname: TextSanitizer::nullablePlainText($validated['nickname'] ?? null),
            locale: TextSanitizer::plainText($validated['locale']),
            timeFormat24h: $validated['time_format_24h'],
        )->execute();

        return new UserResource(Auth::user()->refresh());
    }
}
