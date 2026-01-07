<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleSleep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ModuleSleepTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_journal_entry(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create();
        $moduleSleep = ModuleSleep::factory()->for($entry, 'entry')->create();

        $this->assertTrue($moduleSleep->entry()->exists());
        $this->assertEquals($entry->id, $moduleSleep->entry->id);
    }
}
