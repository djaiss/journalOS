<?php

declare(strict_types=1);

namespace App\View\Presenters;

use App\Models\JournalEntry;

final readonly class SexualActivityModulePresenter
{
    public function __construct(
        private JournalEntry $entry,
    ) {}

    public function build(): array
    {
        $hasSexualActivityUrl = route('journal.entry.sexual-activity.update', [
            'slug' => $this->entry->journal->slug,
            'year' => $this->entry->year,
            'month' => $this->entry->month,
            'day' => $this->entry->day,
        ]);

        $sexualActivityTypeUrl = route('journal.entry.sexual-activity.type.update', [
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

        $activityTypes = collect(['solo', 'with-partner', 'intimate-contact'])->map(fn($type) => [
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
            'has_sexual_activity_url' => $hasSexualActivityUrl,
            'sexual_activity_type_url' => $sexualActivityTypeUrl,
            'sexual_activity_types' => $activityTypes,
            'reset_url' => $resetUrl,
            'display_reset' => ! is_null($this->entry->moduleSexualActivity?->had_sexual_activity)
                || ! is_null($this->entry->moduleSexualActivity?->sexual_activity_type),
        ];
    }
}
