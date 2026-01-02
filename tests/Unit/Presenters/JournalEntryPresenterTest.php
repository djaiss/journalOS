<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
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
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new JournalEntryPresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('sleep', $result);
        $this->assertArrayHasKey('work', $result);
        $this->assertArrayHasKey('travel', $result);
        $this->assertArrayHasKey('day_type', $result);
        $this->assertArrayHasKey('sexual_activity', $result);
    }
}
