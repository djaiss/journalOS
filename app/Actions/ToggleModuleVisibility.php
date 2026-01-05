<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Helpers\TextSanitizer;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ToggleModuleVisibility
{
    public function __construct(
        private User $user,
        private Journal $journal,
        private string $moduleName,
    ) {}

    public function execute(): Journal
    {
        $this->validateModuleName();
        $attribute = $this->moduleAttribute();

        $this->validate($attribute);

        $this->journal->{$attribute} = !$this->journal->{$attribute};
        $this->journal->save();

        $this->logUserAction($attribute);
        $this->updateUserLastActivityDate();

        return $this->journal;
    }

    private function validate(string $attribute): void
    {
        if ($this->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        if ($attribute === 'show__module' || !array_key_exists($attribute, $this->journal->getAttributes())) {
            throw ValidationException::withMessages([
                'module_name' => 'Module not found.',
            ]);
        }

        if (!is_bool($this->journal->{$attribute})) {
            throw ValidationException::withMessages([
                'module_name' => 'Module visibility must be boolean.',
            ]);
        }
    }

    private function validateModuleName(): void
    {
        $this->moduleName = TextSanitizer::plainText($this->moduleName);

        $messages = [];

        if ($this->moduleName === '') {
            $messages['module_name'] = 'Module name must be plain text.';
        }

        if (mb_strlen($this->moduleName) > 255) {
            $messages['module_name'] = 'Module name must not be longer than 255 characters.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }

    private function moduleAttribute(): string
    {
        $normalized = Str::of($this->moduleName)->trim()->snake();

        return 'show_' . $normalized . '_module';
    }

    private function logUserAction(string $attribute): void
    {
        $visibility = $this->journal->{$attribute} ? 'visible' : 'hidden';

        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->journal,
            action: 'module_visibility_toggled',
            description: sprintf(
                'Set %s module visibility to %s',
                Str::of($this->moduleName)->trim(),
                $visibility,
            ),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
