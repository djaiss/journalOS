<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Llm;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\JournalLlmAccessLog;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\ModuleSleep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_markdown_for_a_valid_entry(): void
    {
        $journal = Journal::factory()->create([
            'has_llm_access' => true,
            'llm_access_key' => 'llm-key-123',
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

        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'year' => 2026,
            'month' => 1,
            'day' => 27,
        ]);

        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '23:00',
            'wake_up_time' => '07:15',
            'sleep_duration_in_minutes' => 495,
        ]);

        $response = $this->get('/llm/llm-key-123/2026/1/27');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $response->assertSee('Journal entry — 2026-01-27');
        $response->assertSee('Sleep module');
        $response->assertSee('Bedtime: 23:00');

        $this->assertDatabaseHas('journal_llm_access_logs', [
            'journal_id' => $journal->id,
            'requested_year' => 2026,
            'requested_month' => 1,
            'requested_day' => 27,
        ]);

        $log = JournalLlmAccessLog::query()->first();
        $this->assertNotNull($log);
        $this->assertSame(url('/llm/llm-key-123/2026/1/27'), $log->request_url);
    }

    #[Test]
    public function it_returns_not_found_for_invalid_key(): void
    {
        $response = $this->get('/llm/invalid/2026/1/27');

        $response->assertNotFound();
    }

    #[Test]
    public function it_returns_not_found_for_invalid_date(): void
    {
        $journal = Journal::factory()->create([
            'has_llm_access' => true,
            'llm_access_key' => 'llm-key-123',
        ]);

        $response = $this->get('/llm/' . $journal->llm_access_key . '/2026/2/30');

        $response->assertNotFound();
    }

    #[Test]
    public function it_returns_not_found_when_entry_is_missing(): void
    {
        $journal = Journal::factory()->create([
            'has_llm_access' => true,
            'llm_access_key' => 'llm-key-123',
        ]);

        $response = $this->get('/llm/' . $journal->llm_access_key . '/2026/1/27');

        $response->assertNotFound();
    }
}
