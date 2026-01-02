<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\SexualActivity;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SexualActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_had_sexual_activity_with_yes_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'had_sexual_activity' => null,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/sexual-activity",
            ['had_sexual_activity' => 'yes'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertEquals('yes', $entry->had_sexual_activity);
    }

    #[Test]
    public function it_updates_had_sexual_activity_with_no_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'had_sexual_activity' => null,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/sexual-activity",
            ['had_sexual_activity' => 'no'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertEquals('no', $entry->had_sexual_activity);
    }

    #[Test]
    public function it_validates_had_sexual_activity_must_be_valid(): void
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
            "/journals/{$journal->slug}/entries/2024/6/15/sexual-activity",
            ['had_sexual_activity' => 'invalid'],
        );

        $response->assertSessionHasErrors('had_sexual_activity');
    }

    #[Test]
    public function it_validates_had_sexual_activity_is_required(): void
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
            "/journals/{$journal->slug}/entries/2024/6/15/sexual-activity",
            [],
        );

        $response->assertSessionHasErrors('had_sexual_activity');
    }

    #[Test]
    public function unauthenticated_user_cannot_update_had_sexual_activity(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->put(
            "/journals/{$journal->slug}/entries/2024/6/15/sexual-activity",
            ['had_sexual_activity' => 'yes'],
        );

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function user_cannot_update_another_users_journal_entry(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/sexual-activity",
            ['had_sexual_activity' => 'yes'],
        );

        $response->assertNotFound();
    }
}
