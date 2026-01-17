<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleWeather;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ModuleWeatherTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_journal_entry(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleWeather = ModuleWeather::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);

        $this->assertTrue($moduleWeather->entry()->exists());
        $this->assertEquals($entry->id, $moduleWeather->entry->id);
    }
}
