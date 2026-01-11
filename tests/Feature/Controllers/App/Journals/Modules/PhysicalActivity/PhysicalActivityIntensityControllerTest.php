<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\PhysicalActivity;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PhysicalActivityIntensityControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_activity_intensity_with_light_and_redirects(): void
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
            "/journals/{$journal->slug}/entries/2024/6/15/physical-activity",
            ['activity_intensity' => 'light'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertEquals('light', $entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_updates_activity_intensity_with_moderate_and_redirects(): void
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
            "/journals/{$journal->slug}/entries/2024/6/15/physical-activity",
            ['activity_intensity' => 'moderate'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertEquals('moderate', $entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_updates_activity_intensity_with_intense_and_redirects(): void
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
            "/journals/{$journal->slug}/entries/2024/6/15/physical-activity",
            ['activity_intensity' => 'intense'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertEquals('intense', $entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_validates_activity_intensity_must_be_valid(): void
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
            "/journals/{$journal->slug}/entries/2024/6/15/physical-activity",
            ['activity_intensity' => 'invalid'],
        );

        $response->assertSessionHasErrors('activity_intensity');
    }

    #[Test]
    public function it_validates_activity_intensity_is_required(): void
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
            "/journals/{$journal->slug}/entries/2024/6/15/physical-activity",
            [],
        );

        $response->assertSessionHasErrors('activity_intensity');
    }

    #[Test]
    public function unauthenticated_user_cannot_update_activity_intensity(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->put(
            "/journals/{$journal->slug}/entries/2024/6/15/physical-activity/intensity",
            ['activity_intensity' => 'light'],
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
            "/journals/{$journal->slug}/entries/2024/6/15/physical-activity/intensity",
            ['activity_intensity' => 'light'],
        );

        $response->assertNotFound();
    }
}
