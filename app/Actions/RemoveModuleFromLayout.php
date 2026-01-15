<?php

declare(strict_types=1);

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

final class RemoveModuleFromLayout
{
    private LayoutModule $layoutModule;

    public function __construct(
        private readonly User $user,
        private readonly Layout $layout,
        private string $moduleKey,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->remove();
        $this->updateUserLastActivityDate();
        $this->log();
    }

    private function validate(): void
    {
        $this->moduleKey = mb_strtolower(TextSanitizer::plainText($this->moduleKey));

        if ($this->layout->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Layout not found');
        }

        if ($this->moduleKey === '' || ! in_array($this->moduleKey, LayoutModule::allowedModuleKeys(), true)) {
            throw ValidationException::withMessages([
                'module_key' => 'Module key is invalid',
            ]);
        }

        $this->layoutModule = LayoutModule::query()
            ->where('layout_id', $this->layout->id)
            ->where('module_key', $this->moduleKey)
            ->first() ?? throw ValidationException::withMessages([
                'module_key' => 'Module does not exist in layout',
            ]);
    }

    private function remove(): void
    {
        $columnNumber = $this->layoutModule->column_number;
        $position = $this->layoutModule->position;

        DB::transaction(function () use ($columnNumber, $position): void {
            $this->layoutModule->delete();

            LayoutModule::query()
                ->where('layout_id', $this->layout->id)
                ->where('column_number', $columnNumber)
                ->where('position', '>', $position)
                ->decrement('position');
        });
    }

    private function log(): void
    {
        $journal = $this->layout->journal;

        LogUserAction::dispatch(
            user: $this->user,
            journal: $journal,
            action: 'layout_module_remove',
            description: sprintf(
                'Removed the %s module from the layout %s for the journal %s',
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
