<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AddModuleToLayout
{
    private LayoutModule $layoutModule;
    private int $position;

    public function __construct(
        private readonly User $user,
        private readonly Layout $layout,
        private string $moduleKey,
        private readonly int $columnNumber,
        private readonly ?int $requestedPosition = null,
    ) {}

    public function execute(): LayoutModule
    {
        $this->validate();
        $this->create();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->layoutModule;
    }

    private function validate(): void
    {
        $this->moduleKey = mb_strtolower(TextSanitizer::plainText($this->moduleKey));

        if ($this->layout->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Layout not found');
        }

        if ($this->moduleKey === '' || !in_array($this->moduleKey, LayoutModule::allowedModuleKeys(), true)) {
            throw ValidationException::withMessages([
                'module_key' => 'Module key is invalid',
            ]);
        }

        if ($this->columnNumber < 1 || $this->columnNumber > $this->layout->columns_count) {
            throw ValidationException::withMessages([
                'column_number' => 'Column number must be within the layout columns',
            ]);
        }

        $exists = LayoutModule::query()
            ->where('layout_id', $this->layout->id)
            ->where('module_key', $this->moduleKey)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'module_key' => 'Module already exists in layout',
            ]);
        }

        $currentCount = LayoutModule::query()
            ->where('layout_id', $this->layout->id)
            ->where('column_number', $this->columnNumber)
            ->count();

        $this->position = $this->requestedPosition ?? ( $currentCount + 1 );

        if ($this->position < 1 || $this->position > ( $currentCount + 1 )) {
            throw ValidationException::withMessages([
                'position' => 'Position is invalid for the selected column',
            ]);
        }
    }

    private function create(): void
    {
        DB::transaction(function (): void {
            LayoutModule::query()
                ->where('layout_id', $this->layout->id)
                ->where('column_number', $this->columnNumber)
                ->where('position', '>=', $this->position)
                ->increment('position');

            $this->layoutModule = LayoutModule::query()->create([
                'layout_id' => $this->layout->id,
                'module_key' => $this->moduleKey,
                'column_number' => $this->columnNumber,
                'position' => $this->position,
            ]);
        });
    }

    private function log(): void
    {
        $journal = $this->layout->journal;

        LogUserAction::dispatch(
            user: $this->user,
            journal: $journal,
            action: 'layout_module_add',
            description: sprintf(
                'Added the %s module to the layout %s for the journal %s',
                $this->moduleKey,
                $this->layout->name,
                $journal->name,
            ),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
