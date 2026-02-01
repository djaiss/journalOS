<?php

declare(strict_types = 1);

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $henri = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $henri->id,
        ]);

        $this->assertTrue($journal->user()->exists());
    }

    #[Test]
    public function it_has_many_entries(): void
    {
        $journal = Journal::factory()->create();
        JournalEntry::factory()
            ->count(3)
            ->create([
                'journal_id' => $journal->id,
            ]);

        $this->assertCount(3, $journal->entries);
        $this->assertTrue($journal->entries()->exists());
    }

    #[Test]
    public function it_has_many_layouts(): void
    {
        $journal = Journal::factory()->create();
        Layout::factory()
            ->count(2)
            ->create([
                'journal_id' => $journal->id,
            ]);

        $this->assertCount(2, $journal->layouts);
        $this->assertTrue($journal->layouts()->exists());
    }

    #[Test]
    public function it_gets_the_avatar(): void
    {
        $journal = Journal::factory()->create();

        $this->assertIsString($journal->avatar());
    }
}
