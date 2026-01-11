<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\SocialDensity;

use App\Actions\LogSocialDensity;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModuleSocialDensity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;

final class SocialDensityController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'social_density' => ['required', 'string', 'max:255', Rule::in(ModuleSocialDensity::SOCIAL_DENSITY_VALUES)],
        ]);

        new LogSocialDensity(
            user: Auth::user(),
            entry: $entry,
            socialDensity: TextSanitizer::plainText($validated['social_density']),
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
