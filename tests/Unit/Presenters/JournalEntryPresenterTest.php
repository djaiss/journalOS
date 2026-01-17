<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\View\Presenters\JournalEntryPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class JournalEntryPresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_journal_entry_data(): void
    {
        $journal = Journal::factory()->create();
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 2,
            'is_active' => true,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'sleep',
            'column_number' => 1,
            'position' => 1,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'work',
            'column_number' => 1,
            'position' => 2,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'mood',
            'column_number' => 2,
            'position' => 1,
        ]);

        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'weather_influence',
            'column_number' => 2,
            'position' => 2,
        ]);

        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new JournalEntryPresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('columns', $result);
        $this->assertArrayHasKey('notes', $result);
        $this->assertSame(2, $result['layout_columns_count']);
        $this->assertArrayHasKey(1, $result['columns']);
        $this->assertArrayHasKey(2, $result['columns']);
        $this->assertSame('sleep', $result['columns'][1][0]['key']);
        $this->assertSame('work', $result['columns'][1][1]['key']);
        $this->assertSame('mood', $result['columns'][2][0]['key']);
        $this->assertSame('weather_influence', $result['columns'][2][1]['key']);
    }
}
