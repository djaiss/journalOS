<?php

namespace Tests\Unit\Services;

use App\Models\Journal;
use App\Models\User;
use App\Services\UpdateJournal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateJournalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_update_a_journal(): void
    {
        $user = User::factory()->create();
        $this->be($user);
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $journal = (new UpdateJournal(
            journal: $journal,
            name: 'this is the new name',
        ))->execute();

        $this->assertInstanceOf(
            Journal::class,
            $journal
        );

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'user_id' => $user->id,
            'name' => 'this is the new name',
        ]);
    }

    /** @test */
    public function a_user_cant_update_a_journal(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $this->be($user);
        $journal = Journal::factory()->create();

        (new UpdateJournal(
            journal: $journal,
            name: 'this is the new name',
        ))->execute();
    }
}
