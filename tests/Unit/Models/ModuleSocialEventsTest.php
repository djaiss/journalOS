<?php

declare(strict_types = 1);

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleSocialEvents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ModuleSocialEventsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_journal_entry(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleSocialEvents = ModuleSocialEvents::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);

        $this->assertTrue($moduleSocialEvents->entry()->exists());
        $this->assertEquals($entry->id, $moduleSocialEvents->entry->id);
    }
}
