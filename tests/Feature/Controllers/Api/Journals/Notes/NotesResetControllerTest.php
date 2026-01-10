<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals\Notes;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class NotesResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_notes_and_returns_the_journal_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);
        $entry->richTextNotes()->create([
            'field' => 'notes',
            'body' => '<p>Existing notes.</p>',
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/notes/reset");

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.notes', null);

        $entry->refresh()->load('richTextNotes');
        $this->assertEquals('', $entry->richTextNotes->toPlainText());
    }
}
