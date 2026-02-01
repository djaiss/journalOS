<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals;

use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $collectionJsonStructure = [
        'data' => [
            '*' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'slug',
                    'avatar',
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
            ],
        ],
    ];

    private array $singleJsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'name',
                'slug',
                'avatar',
                'created_at',
                'updated_at',
            ],
            'links' => [
                'self',
            ],
        ],
    ];

    #[Test]
    public function it_can_list_the_journals_of_the_current_user(): void
    {
        $user = User::factory()->create();

        $dunderMifflin = Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Dunder Mifflin',
        ]);
        $vancerefrigeration = Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Vance refrigeration',
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/journals');

        $response->assertStatus(200);
        $response->assertJsonStructure($this->collectionJsonStructure);
        $response->assertJsonCount(2, 'data');

        $response->assertJson([
            'data' => [
                [
                    'type' => 'journal',
                    'id' => (string) $dunderMifflin->id,
                    'attributes' => [
                        'name' => 'Dunder Mifflin',
                        'slug' => $dunderMifflin->slug,
                    ],
                ],
                [
                    'type' => 'journal',
                    'id' => (string) $vancerefrigeration->id,
                    'attributes' => [
                        'name' => 'Vance refrigeration',
                        'slug' => $vancerefrigeration->slug,
                    ],
                ],
            ],
        ]);
    }

    #[Test]
    public function it_returns_empty_collection_when_user_has_no_journals(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/journals');

        $response->assertStatus(200);
        $response->assertJsonStructure($this->collectionJsonStructure);
        $response->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_can_create_a_new_journal(): void
    {
        Carbon::setTestNow('2025-01-01 00:00:00');
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/journals', [
            'name' => 'Dunder Mifflin',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure($this->singleJsonStructure);

        $journal = Journal::latest()->first();
        $this->assertEquals('Dunder Mifflin', $journal->name);

        $response->assertJson([
            'data' => [
                'type' => 'journal',
                'id' => (string) $journal->id,
                'attributes' => [
                    'name' => 'Dunder Mifflin',
                    'slug' => $journal->slug,
                    'created_at' => Carbon::now()->timestamp,
                    'updated_at' => Carbon::now()->timestamp,
                ],
            ],
        ]);
    }

    #[Test]
    public function it_sanitizes_journal_names(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/journals', [
            'name' => '<b>Dunder</b> Mifflin',
        ]);

        $response->assertStatus(201);

        $journal = Journal::latest()->first();

        $this->assertEquals('Dunder Mifflin', $journal->name);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'name' => 'Dunder Mifflin',
                ],
            ],
        ]);
    }

    #[Test]
    public function it_can_show_a_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/journals/' . $journal->id);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->singleJsonStructure);
    }

    #[Test]
    public function it_restricts_access_to_a_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/journals/' . $journal->id);

        $response->assertStatus(404);
    }

    #[Test]
    public function it_can_update_the_journal(): void
    {
        Carbon::setTestNow('2025-01-01 00:00:00');
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/journals/' . $journal->id, [
            'name' => 'Dunder Mifflin Michael Scott Edition',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->singleJsonStructure);

        $journal = Journal::latest()->first();
        $this->assertEquals('Dunder Mifflin Michael Scott Edition', $journal->name);

        $response->assertJson([
            'data' => [
                'type' => 'journal',
                'id' => (string) $journal->id,
                'attributes' => [
                    'name' => 'Dunder Mifflin Michael Scott Edition',
                    'slug' => $journal->slug,
                    'created_at' => Carbon::now()->timestamp,
                    'updated_at' => Carbon::now()->timestamp,
                ],
            ],
        ]);
    }

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
