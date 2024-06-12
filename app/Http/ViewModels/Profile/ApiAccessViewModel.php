<?php

namespace App\Http\ViewModels\Profile;

use Laravel\Sanctum\PersonalAccessToken;

class ApiAccessViewModel
{
    public static function index(): array
    {
        $tokens = auth()->user()->tokens
            ->map(fn (PersonalAccessToken $token) => [
                'id' => $token->id,
                'name' => $token->name,
                'last_used' => $token->last_used_at ? $token?->last_used_at->diffForHumans() : trans('Never'),
            ]);

        return [
            'tokens' => $tokens,
        ];
    }
}
