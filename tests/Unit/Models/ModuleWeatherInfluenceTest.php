<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\JournalEntry;
use App\Models\ModuleWeatherInfluence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ModuleWeatherInfluenceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_journal_entry(): void
    {
        $entry = JournalEntry::factory()->create();
        $moduleWeatherInfluence = ModuleWeatherInfluence::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);

        $this->assertTrue($moduleWeatherInfluence->entry()->exists());
        $this->assertEquals($entry->id, $moduleWeatherInfluence->entry->id);
    }
}
