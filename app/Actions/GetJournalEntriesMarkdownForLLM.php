<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final readonly class GetJournalEntriesMarkdownForLLM
{
    public function __construct(
        private Journal $journal,
        private int $year,
        private ?int $month,
    ) {}

    public function execute(): string
    {
        $this->validateDate();

        $entries = $this->retrieveEntries();
        $activeLayout = $this->resolveActiveLayout();

        return $this->buildMarkdown($entries, $activeLayout);
    }

    private function validateDate(): void
    {
        $month = $this->month ?? 1;

        if (! checkdate($month, 1, $this->year)) {
            throw ValidationException::withMessages([
                'date' => 'Date is invalid.',
            ]);
        }
    }

    /**
     * @return Collection<int, JournalEntry>
     */
    private function retrieveEntries(): Collection
    {
        $relationships = array_values(array_unique(array_merge(
            GetJournalEntryMarkdownForLLM::allRelationships(),
            ['richTextNotes', 'layout.layoutModules'],
        )));

        $entries = JournalEntry::query()
            ->where('journal_id', $this->journal->id)
            ->where('year', $this->year)
            ->when($this->month !== null, function ($query): void {
                $query->where('month', $this->month);
            })
            ->orderBy('year')
            ->orderBy('month')
            ->orderBy('day')
            ->with($relationships)
            ->get();

        if ($entries->isEmpty()) {
            throw new ModelNotFoundException()->setModel(JournalEntry::class);
        }

        return $entries;
    }

    private function resolveActiveLayout(): ?Layout
    {
        return $this->journal->layouts()
            ->where('is_active', true)
            ->with(['layoutModules' => function ($query): void {
                $query->orderBy('column_number')
                    ->orderBy('position');
            }])
            ->first();
    }

    /**
     * @param  Collection<int, JournalEntry>  $entries
     */
    private function buildMarkdown(Collection $entries, ?Layout $activeLayout): string
    {
        $lines = [];
        $lines[] = $this->month
            ? sprintf('# Journal entries — %04d-%02d', $this->year, $this->month)
            : sprintf('# Journal entries — %04d', $this->year);
        $lines[] = sprintf('Journal: %s', $this->journal->name);
        $lines[] = '';

        foreach ($entries as $entry) {
            $layout = $entry->layout ?? $activeLayout;
            $moduleKeys = GetJournalEntryMarkdownForLLM::moduleKeysForLayout($layout);

            $lines[] = sprintf('## %04d-%02d-%02d', $entry->year, $entry->month, $entry->day);

            $content = GetJournalEntryMarkdownForLLM::entryContent($entry, $moduleKeys);
            $lines[] = $content !== '' ? $content : 'No data.';
            $lines[] = '';
        }

        return mb_rtrim(implode("\n", $lines)) . "\n";
    }
}
