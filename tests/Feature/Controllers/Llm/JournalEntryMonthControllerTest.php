<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Llm;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\JournalLlmAccessLog;
use App\Models\Layout;
use App\Models\LayoutModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalEntryMonthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_markdown_for_a_month_request(): void
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

        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'year' => 2026,
            'month' => 1,
            'day' => 27,
        ]);

        $response = $this->get('/llm/llm-key-123/2026/1');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $response->assertSee('Journal entries — 2026-01');
        $response->assertSee('## 2026-01-27');

        $this->assertDatabaseHas('journal_llm_access_logs', [
            'journal_id' => $journal->id,
            'requested_year' => 2026,
            'requested_month' => 1,
            'requested_day' => null,
        ]);

        $log = JournalLlmAccessLog::query()->first();
        $this->assertNotNull($log);
        $this->assertSame(url('/llm/llm-key-123/2026/1'), $log->request_url);
    }

    #[Test]
    public function it_returns_not_found_for_invalid_month_request(): void
    {
        $journal = Journal::factory()->create([
            'has_llm_access' => true,
            'llm_access_key' => 'llm-key-123',
        ]);

        $response = $this->get('/llm/' . $journal->llm_access_key . '/2026/13');

        $response->assertNotFound();
    }

}
