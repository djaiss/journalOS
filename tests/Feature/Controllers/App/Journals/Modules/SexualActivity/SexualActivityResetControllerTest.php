<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\SexualActivity;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SexualActivityResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_sexual_activity_data_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'had_sexual_activity' => 'yes',
            'sexual_activity_type' => 'solo',
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/sexual-activity/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2022/1/1");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertNull($entry->had_sexual_activity);
        $this->assertNull($entry->sexual_activity_type);
    }

    #[Test]
    public function unauthenticated_user_cannot_reset_sexual_activity_data(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'had_sexual_activity' => 'yes',
            'sexual_activity_type' => 'solo',
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->put(
            "/journals/{$journal->slug}/entries/2022/1/1/sexual-activity/reset",
        );

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function user_cannot_reset_another_users_sexual_activity_data(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'had_sexual_activity' => 'yes',
            'sexual_activity_type' => 'solo',
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/sexual-activity/reset",
        );

        $response->assertNotFound();
    }
}
