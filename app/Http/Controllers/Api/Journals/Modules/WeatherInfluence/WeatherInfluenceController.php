<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\WeatherInfluence;

use App\Actions\LogWeatherInfluence;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleWeatherInfluence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class WeatherInfluenceController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'mood_effect' => ['nullable', 'string', 'max:255', Rule::in(ModuleWeatherInfluence::MOOD_EFFECTS), 'required_without_all:energy_effect,plans_influence,outside_time'],
            'energy_effect' => ['nullable', 'string', 'max:255', Rule::in(ModuleWeatherInfluence::ENERGY_EFFECTS), 'required_without_all:mood_effect,plans_influence,outside_time'],
            'plans_influence' => ['nullable', 'string', 'max:255', Rule::in(ModuleWeatherInfluence::PLANS_INFLUENCES), 'required_without_all:mood_effect,energy_effect,outside_time'],
            'outside_time' => ['nullable', 'string', 'max:255', Rule::in(ModuleWeatherInfluence::OUTSIDE_TIMES), 'required_without_all:mood_effect,energy_effect,plans_influence'],
        ]);

        $entry = new LogWeatherInfluence(
            user: Auth::user(),
            entry: $entry,
            moodEffect: array_key_exists('mood_effect', $validated) ? TextSanitizer::plainText($validated['mood_effect']) : null,
            energyEffect: array_key_exists('energy_effect', $validated) ? TextSanitizer::plainText($validated['energy_effect']) : null,
            plansInfluence: array_key_exists('plans_influence', $validated) ? TextSanitizer::plainText($validated['plans_influence']) : null,
            outsideTime: array_key_exists('outside_time', $validated) ? TextSanitizer::plainText($validated['outside_time']) : null,
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
