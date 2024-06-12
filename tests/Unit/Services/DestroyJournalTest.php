<?php

namespace Tests\Unit\Services;

use App\Models\Journal;
use App\Models\User;
use App\Services\DestroyJournal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyJournalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_destroy_a_journal(): void
    {
        $user = User::factory()->create();
        $this->be($user);
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        (new DestroyJournal(
            journal: $journal,
        ))->execute();

        $this->assertDatabaseMissing('journals', [
            'id' => $journal->id,
        ]);
    }

    /** @test */
    public function a_user_cant_delete_a_journal(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $this->be($user);
        $journal = Journal::factory()->create();

        (new DestroyJournal(
            journal: $journal,
        ))->execute();
    }
}
