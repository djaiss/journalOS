<?php

declare(strict_types = 1);

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LayoutTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_journal(): void
    {
        $journal = Journal::factory()->create();
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->assertTrue($layout->journal()->exists());
    }

    #[Test]
    public function it_has_many_entries(): void
    {
        $journal = Journal::factory()->create();
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);
        JournalEntry::factory()
            ->count(2)
            ->create([
                'journal_id' => $journal->id,
                'layout_id' => $layout->id,
            ]);

        $this->assertCount(2, $layout->entries);
        $this->assertTrue($layout->entries()->exists());
    }
}
