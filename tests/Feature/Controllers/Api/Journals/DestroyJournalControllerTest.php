<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DestroyJournalControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_delete_a_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', '/api/journals/' . $journal->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('journals', [
            'id' => $journal->id,
        ]);
    }

    #[Test]
    public function it_returns_not_found_when_deleting_a_journal_the_user_does_not_own(): void
    {
        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', '/api/journals/' . $otherJournal->id);

        $response->assertStatus(404);
    }
}
