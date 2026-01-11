<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\SocialDensity;

use App\Actions\LogSocialDensity;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleSocialDensity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class SocialDensityController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'social_density' => ['required', 'string', 'max:255', Rule::in(ModuleSocialDensity::SOCIAL_DENSITY_VALUES)],
        ]);

        $entry = new LogSocialDensity(
            user: Auth::user(),
            entry: $entry,
            socialDensity: TextSanitizer::plainText($validated['social_density']),
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
