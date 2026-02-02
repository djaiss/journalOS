<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals\Modules\SocialEvents;

use App\Actions\LogSocialEvents;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\ModuleSocialEvents;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class SocialEventsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'event_type' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleSocialEvents::EVENT_TYPE_VALUES),
                'required_without_all:tone,duration',
            ],
            'tone' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleSocialEvents::TONE_VALUES),
                'required_without_all:event_type,duration',
            ],
            'duration' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleSocialEvents::DURATION_VALUES),
                'required_without_all:event_type,tone',
            ],
        ]);

        new LogSocialEvents(
            user: Auth::user(),
            entry: $entry,
            eventType: array_key_exists('event_type', $validated) && $validated['event_type'] !== null
                ? TextSanitizer::plainText($validated['event_type'])
                : null,
            tone: array_key_exists('tone', $validated) && $validated['tone'] !== null
                ? TextSanitizer::plainText($validated['tone'])
                : null,
            duration: array_key_exists('duration', $validated) && $validated['duration'] !== null
                ? TextSanitizer::plainText($validated['duration'])
                : null,
        )->execute();

        return to_route('journal.entry.edit', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }
}
