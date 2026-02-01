<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Models\JournalEntry;
use App\Models\ModuleSexualActivity;

final readonly class SexualActivityModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $sexualActivityUrl = route('journal.entry.sexual-activity.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $resetUrl = route('journal.entry.sexual-activity.reset', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $activityTypes = collect(ModuleSexualActivity::SEXUAL_ACTIVITY_TYPES)->map(fn ($type) => [
            'value' => $type,
            'label' => match ($type) {
                'solo' => __('Solo'),
                'with-partner' => __('With partner'),
                'intimate-contact' => __('Intimate contact'),
                default => $type,
            },
            'is_selected' => $type === $this->entry->moduleSexualActivity?->sexual_activity_type,
        ]);

        return [
            'sexual_activity_url' => $sexualActivityUrl,
            'sexual_activity_types' => $activityTypes,
            'reset_url' => $resetUrl,
            'display_reset' =>
                !is_null($this->entry->moduleSexualActivity?->had_sexual_activity)
                    || !is_null($this->entry->moduleSexualActivity?->sexual_activity_type),
        ];
    }
}
