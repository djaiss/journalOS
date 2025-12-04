<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_journal(): void
    {
        $journal = Journal::factory()->create();
        $journalEntry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->assertTrue($journalEntry->journal()->exists());
    }

    #[Test]
    public function it_gets_the_date(): void
    {
        $journalEntry = JournalEntry::factory()->create([
            'day' => 1,
            'month' => 1,
            'year' => 2021,
        ]);

        $this->assertEquals('Friday January 1st, 2021', $journalEntry->getDate());
    }
}
