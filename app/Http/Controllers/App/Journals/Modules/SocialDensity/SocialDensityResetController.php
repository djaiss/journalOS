<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\SocialDensity;

use App\Actions\ResetSocialDensityData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class SocialDensityResetController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        new ResetSocialDensityData(
            user: Auth::user(),
            entry: $entry,
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
