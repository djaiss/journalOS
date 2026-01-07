<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Travel;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TravelModeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_travel_modes_with_single_mode(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/travel/mode",
            ['travel_modes' => ['car']],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleTravel');
        $this->assertEquals(['car'], $entry->moduleTravel->travel_mode);
    }

    #[Test]
    public function it_updates_travel_modes_with_multiple_modes(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/travel/mode",
            ['travel_modes' => ['car', 'plane', 'train']],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleTravel');
        $this->assertEquals(['car', 'plane', 'train'], $entry->moduleTravel->travel_mode);
        $this->assertContains('car', $entry->moduleTravel->travel_mode);
        $this->assertContains('plane', $entry->moduleTravel->travel_mode);
        $this->assertContains('train', $entry->moduleTravel->travel_mode);
    }

    #[Test]
    public function it_updates_with_all_travel_modes(): void
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

        $allModes = ['car', 'plane', 'train', 'bike', 'bus', 'walk', 'boat', 'other'];

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/travel/mode",
            ['travel_modes' => $allModes],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleTravel');
        $this->assertEquals($allModes, $entry->moduleTravel->travel_mode);
        $this->assertCount(8, $entry->moduleTravel->travel_mode);
    }

    #[Test]
    public function it_validates_travel_modes_must_be_array(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/travel/mode",
            ['travel_modes' => 'car'],
        );

        $response->assertSessionHasErrors('travel_modes');
    }

    #[Test]
    public function it_validates_travel_modes_cannot_be_empty(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/travel/mode",
            ['travel_modes' => []],
        );

        $response->assertSessionHasErrors('travel_modes');
    }

    #[Test]
    public function it_validates_each_travel_mode_must_be_valid(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/travel/mode",
            ['travel_modes' => ['car', 'invalid', 'plane']],
        );

        $response->assertSessionHasErrors('travel_modes.1');
    }

    #[Test]
    public function it_validates_travel_modes_is_required(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/travel/mode",
            [],
        );

        $response->assertSessionHasErrors('travel_modes');
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->put(
            "/journals/{$journal->slug}/entries/2024/6/15/travel/mode",
            ['travel_modes' => ['car']],
        );

        $response->assertRedirect('/login');
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/travel/mode",
            ['travel_modes' => ['car']],
        );

        $response->assertNotFound();
    }
}
