<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Journals\Modules\DayType;

use App\Actions\LogTypeOfDay;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleDayType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class DayTypeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'day_type' => ['required', 'string', 'max:255', Rule::in(ModuleDayType::DAY_TYPES)],
        ]);

        $entry = new LogTypeOfDay(
            user: Auth::user(),
            entry: $journalEntry,
            dayType: TextSanitizer::plainText($validated['day_type']),
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
