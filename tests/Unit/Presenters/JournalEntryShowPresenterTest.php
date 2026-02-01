<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Book;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\ModuleHealth;
use App\Models\ModuleWork;
use App\View\Presenters\JournalEntryShowPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalEntryShowPresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_only_modules_with_data_in_layout_order(): void
    {
        $journal = Journal::factory()->create();
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 1,
            'is_active' => true,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'travel',
            'column_number' => 1,
            'position' => 1,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'health',
            'column_number' => 1,
            'position' => 2,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'reading',
            'column_number' => 1,
            'position' => 3,
        ]);

        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'year' => 2025,
            'month' => 1,
            'day' => 4,
        ]);

        ModuleHealth::factory()->create([
            'journal_entry_id' => $entry->id,
            'health' => 'great',
        ]);

        $book = Book::factory()->create();
        $entry->books()->attach($book->id, ['status' => 'finished']);

        $result = new JournalEntryShowPresenter($entry)->build();

        $this->assertCount(2, $result['modules']);
        $this->assertSame('health', $result['modules'][0]['key']);
        $this->assertSame('reading', $result['modules'][1]['key']);
    }

    #[Test]
    public function it_filters_empty_values_from_module_rows(): void
    {
        $journal = Journal::factory()->create();
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 1,
            'is_active' => true,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'work',
            'column_number' => 1,
            'position' => 1,
        ]);

        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
        ]);

        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'worked' => 'yes',
            'work_mode' => null,
            'work_load' => null,
            'work_procrastinated' => null,
        ]);

        $result = new JournalEntryShowPresenter($entry)->build();

        $this->assertCount(1, $result['modules']);
        $this->assertSame('work', $result['modules'][0]['key']);
        $this->assertCount(1, $result['modules'][0]['rows']);
        $this->assertSame('Worked', $result['modules'][0]['rows'][0]['label']);
        $this->assertSame('Yes', $result['modules'][0]['rows'][0]['value']);
    }
}
