<?php

namespace Tests\Feature\Api\Organization;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JournalControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_journal(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/journals', [
            'name' => 'New journal',
        ]);

        $response->assertStatus(201);

        $journal = Journal::latest('id')->first();

        $this->assertEquals(
            [
                'id' => $journal->id,
                'object' => 'journal',
                'name' => 'New journal',
            ],
            $response->json()
        );
    }

    /** @test */
    public function it_updates_a_journal(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old journal',
        ]);

        $response = $this->json('PUT', '/api/journals/' . $journal->id, [
            'name' => 'New journal',
        ]);

        $response->assertStatus(200);

        $this->assertEquals(
            [
                'id' => $journal->id,
                'object' => 'journal',
                'name' => 'New journal',
            ],
            $response->json()
        );
    }

    /** @test */
    public function it_cant_update_a_journal(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'name' => 'Old journal',
        ]);

        $response = $this->json('PUT', '/api/journals/' . $journal->id, [
            'name' => 'New journal',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_deletes_a_journal(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old journal',
        ]);

        $response = $this->json('DELETE', '/api/journals/' . $journal->id);

        $response->assertStatus(200);

        $this->assertEquals(
            [
                'status' => 'success',
            ],
            $response->json()
        );
    }

    /** @test */
    public function it_cant_delete_a_journal(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'name' => 'Old journal',
        ]);

        $response = $this->json('DELETE', '/api/journals/' . $journal->id);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_lists_all_the_journals(): void
    {
        $user = User::factory()->create();
        $journal = $user->journals()->create([
            'name' => 'New journal',
        ]);
        $secondJournal = $user->journals()->create([
            'name' => 'Old journal',
        ]);
        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/journals');

        $response->assertStatus(200);

        $this->assertEquals(
            $response->json(),
            [
                0 => [
                    'id' => $journal->id,
                    'object' => 'journal',
                    'name' => 'New journal',
                ],
                1 => [
                    'id' => $secondJournal->id,
                    'object' => 'journal',
                    'name' => 'Old journal',
                ],
            ]
        );
    }
}
