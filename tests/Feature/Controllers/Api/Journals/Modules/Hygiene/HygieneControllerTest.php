<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Hygiene;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HygieneControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_hygiene_with_showered_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/hygiene", [
            'showered' => 'yes',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'hygiene' => [
                            'showered' => 'yes',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('moduleHygiene');
        $this->assertEquals('yes', $entry->moduleHygiene->showered);
    }

    #[Test]
    public function it_updates_hygiene_with_brushed_teeth_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/hygiene", [
            'brushed_teeth' => 'pm',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'hygiene' => [
                            'brushed_teeth' => 'pm',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('moduleHygiene');
        $this->assertEquals('pm', $entry->moduleHygiene->brushed_teeth);
    }

    #[Test]
    public function it_updates_hygiene_with_skincare_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/hygiene", [
            'skincare' => 'no',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'hygiene' => [
                            'skincare' => 'no',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('moduleHygiene');
        $this->assertEquals('no', $entry->moduleHygiene->skincare);
    }

    #[Test]
    public function it_validates_hygiene_must_be_valid(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/hygiene", [
            'showered' => 'maybe',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('showered');
    }

    #[Test]
    public function it_validates_hygiene_is_required(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/hygiene", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['showered', 'brushed_teeth', 'skincare']);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/hygiene", [
            'showered' => 'yes',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/hygiene", [
            'showered' => 'yes',
        ]);

        $response->assertStatus(404);
    }
}
