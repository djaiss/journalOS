<?php

namespace Tests\Unit\Services;

use App\Models\Journal;
use App\Models\User;
use App\Services\CreateJournal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateJournalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_journal(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $journal = (new CreateJournal(
            name: 'Nice journal',
        ))->execute();

        $this->assertInstanceOf(
            Journal::class,
            $journal
        );

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'user_id' => $user->id,
            'name' => 'Nice journal',
        ]);
    }
}
