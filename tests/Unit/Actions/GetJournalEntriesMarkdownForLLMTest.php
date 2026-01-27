<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\GetJournalEntriesMarkdownForLLM;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\ModuleMood;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class GetJournalEntriesMarkdownForLLMTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_markdown_for_a_month_range(): void
    {
        $journal = Journal::factory()->create([
            'name' => 'Gamma Journal',
        ]);

        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'is_active' => true,
            'columns_count' => 1,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'mood',
            'column_number' => 1,
            'position' => 1,
        ]);

        $entryOne = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'year' => 2026,
            'month' => 1,
            'day' => 27,
        ]);

        $entryTwo = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'year' => 2026,
            'month' => 1,
            'day' => 28,
        ]);

        ModuleMood::factory()->create([
            'journal_entry_id' => $entryOne->id,
            'mood' => 'calm',
        ]);

        $markdown = (new GetJournalEntriesMarkdownForLLM(
            journal: $journal,
            year: 2026,
            month: 1,
        ))->execute();

        $this->assertStringContainsString('Journal entries — 2026-01', $markdown);
        $this->assertStringContainsString('Journal: Gamma Journal', $markdown);
        $this->assertStringContainsString('## 2026-01-27', $markdown);
        $this->assertStringContainsString('### Mood module', $markdown);
        $this->assertStringContainsString('Mood: calm', $markdown);
        $this->assertStringContainsString('## 2026-01-28', $markdown);
        $this->assertStringContainsString('- No data', $markdown);
    }
}
