<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Journals\Modules\Energy;

use App\Actions\LogEnergy;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleEnergy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class EnergyController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'energy' => ['required', 'string', 'max:255', Rule::in(ModuleEnergy::ENERGY_LEVELS)],
        ]);

        $entry = new LogEnergy(
            user: Auth::user(),
            entry: $entry,
            energy: TextSanitizer::plainText($validated['energy']),
        )->execute();

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }
}
