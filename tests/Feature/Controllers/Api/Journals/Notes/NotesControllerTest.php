<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Notes;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class NotesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_notes_and_returns_the_journal_entry(): void
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

        Sanctum::actingAs($user);

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/notes", [
            'notes' => '<p>Notes for the day.</p>',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'journal_id',
                    'day',
                    'month',
                    'year',
                    'notes',
                    'modules',
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);

        $entry->refresh()->load('richTextNotes');
        $expectedNotes = mb_trim($entry->richTextNotes->render());

        $response->assertJsonPath('data.attributes.notes', $expectedNotes);
        $this->assertEquals('Notes for the day.', mb_trim($entry->richTextNotes->toPlainText()));
    }
}
