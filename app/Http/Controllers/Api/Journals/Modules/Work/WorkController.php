<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Journals\Modules\Work;

use App\Actions\LogWork;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\ModuleWork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class WorkController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $journalEntry = $request->attributes->get('journal_entry');
        $validated = $request->validate([
            'worked' => [
                'nullable',
                'string',
                'max:255',
                'in:yes,no',
                'required_without_all:work_mode,work_load,work_procrastinated',
            ],
            'work_mode' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleWork::WORK_MODES),
                'required_without_all:worked,work_load,work_procrastinated',
            ],
            'work_load' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(ModuleWork::WORK_LOADS),
                'required_without_all:worked,work_mode,work_procrastinated',
            ],
            'work_procrastinated' => [
                'nullable',
                'string',
                'max:255',
                'in:yes,no',
                'required_without_all:worked,work_mode,work_load',
            ],
        ]);

        $entry = new LogWork(
            user: Auth::user(),
            entry: $journalEntry,
            worked: array_key_exists('worked', $validated)
                ? TextSanitizer::nullablePlainText($validated['worked'])
                : null,
            workMode: array_key_exists('work_mode', $validated)
                ? TextSanitizer::nullablePlainText($validated['work_mode'])
                : null,
            workLoad: array_key_exists('work_load', $validated)
                ? TextSanitizer::nullablePlainText($validated['work_load'])
                : null,
            workProcrastinated: array_key_exists('work_procrastinated', $validated)
                ? TextSanitizer::nullablePlainText($validated['work_procrastinated'])
                : null,
        )->execute();

        return new JournalEntryResource($entry)
            ->response()
            ->setStatusCode(200);
    }
}
