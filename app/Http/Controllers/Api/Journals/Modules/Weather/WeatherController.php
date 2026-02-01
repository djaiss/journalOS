<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Journals\Modules\Weather;

use App\Actions\LogWeather;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleWeather;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class WeatherController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'condition' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleWeather::CONDITIONS),
                'required_without_all:temperature_range,precipitation,daylight',
            ],
            'temperature_range' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleWeather::TEMPERATURE_RANGES),
                'required_without_all:condition,precipitation,daylight',
            ],
            'precipitation' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleWeather::PRECIPITATION_LEVELS),
                'required_without_all:condition,temperature_range,daylight',
            ],
            'daylight' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleWeather::DAYLIGHT_VALUES),
                'required_without_all:condition,temperature_range,precipitation',
            ],
        ]);

        $entry = new LogWeather(
            user: Auth::user(),
            entry: $entry,
            condition: array_key_exists('condition', $validated)
                ? TextSanitizer::plainText($validated['condition'])
                : null,
            temperatureRange: array_key_exists('temperature_range', $validated)
                ? TextSanitizer::plainText($validated['temperature_range'])
                : null,
            precipitation: array_key_exists('precipitation', $validated)
                ? TextSanitizer::plainText($validated['precipitation'])
                : null,
            daylight: array_key_exists('daylight', $validated)
                ? TextSanitizer::plainText($validated['daylight'])
                : null,
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
