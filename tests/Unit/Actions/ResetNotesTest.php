<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\ResetNotes;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ResetNotesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_resets_notes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'notes' => '<p>Some notes content</p>',
        ]);

        $this->assertNotNull($entry->notes);

        new ResetNotes(
            user: $user,
            entry: $entry,
        )->execute();

        $entry->refresh();

        $this->assertSame('', mb_trim($entry->notes->toPlainText()));
        $this->assertDatabaseHas('rich_texts', [
            'record_type' => JournalEntry::class,
            'record_id' => $entry->id,
            'field' => 'notes',
        ]);
    }

    #[Test]
    public function it_throws_exception_if_entry_does_not_belong_to_user(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $anotherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->expectException(ModelNotFoundException::class);

        new ResetNotes(
            user: $user,
            entry: $entry,
        )->execute();
    }
}
