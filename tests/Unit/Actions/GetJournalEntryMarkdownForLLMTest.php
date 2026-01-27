<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\GetJournalEntryMarkdownForLLM;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\ModuleMood;
use App\Models\ModuleSleep;
use App\Models\ModuleWork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class GetJournalEntryMarkdownForLLMTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_markdown_with_modules_from_layout_only(): void
    {
        $journal = Journal::factory()->create([
            'name' => 'Dunder Mifflin',
        ]);

        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'is_active' => true,
            'columns_count' => 1,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'sleep',
            'column_number' => 1,
            'position' => 1,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'mood',
            'column_number' => 1,
            'position' => 2,
        ]);

        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'year' => 2026,
            'month' => 1,
            'day' => 27,
        ]);

        $entry->notes = 'Notes about the day.';
        $entry->save();

        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '22:30',
            'wake_up_time' => '06:45',
            'sleep_duration_in_minutes' => 495,
        ]);

        ModuleMood::factory()->create([
            'journal_entry_id' => $entry->id,
            'mood' => 'happy',
        ]);

        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'worked' => 'yes',
        ]);

        $markdown = (new GetJournalEntryMarkdownForLLM(
            journal: $journal,
            year: 2026,
            month: 1,
            day: 27,
        ))->execute();

        $this->assertStringContainsString('Journal entry — 2026-01-27', $markdown);
        $this->assertStringContainsString('Journal: Dunder Mifflin', $markdown);
        $this->assertStringContainsString('## Notes', $markdown);
        $this->assertStringContainsString('Notes about the day.', $markdown);
        $this->assertStringContainsString('### Sleep module', $markdown);
        $this->assertStringContainsString('Bedtime: 22:30', $markdown);
        $this->assertStringContainsString('### Mood module', $markdown);
        $this->assertStringContainsString('Mood: happy', $markdown);
        $this->assertStringNotContainsString('### Work module', $markdown);
    }
}
