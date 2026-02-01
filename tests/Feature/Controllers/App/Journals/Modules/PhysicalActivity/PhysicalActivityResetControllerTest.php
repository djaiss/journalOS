<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Journals\Modules\PhysicalActivity;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModulePhysicalActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PhysicalActivityResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_physical_activity_data_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);
        ModulePhysicalActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_done_physical_activity' => 'yes',
            'activity_type' => 'running',
            'activity_intensity' => 'moderate',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/physical-activity/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2022/1/1");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertNull($entry->modulePhysicalActivity->has_done_physical_activity);
        $this->assertNull($entry->modulePhysicalActivity->activity_type);
        $this->assertNull($entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->put("/journals/{$journal->slug}/entries/2022/1/1/physical-activity/reset");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);
        ModulePhysicalActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_done_physical_activity' => 'yes',
            'activity_type' => 'swimming',
            'activity_intensity' => 'intense',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/physical-activity/reset",
        );

        $response->assertStatus(404);

        $entry->refresh();
        $this->assertNotNull($entry->modulePhysicalActivity->has_done_physical_activity);
        $this->assertNotNull($entry->modulePhysicalActivity->activity_type);
        $this->assertNotNull($entry->modulePhysicalActivity->activity_intensity);
    }
}
