<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleKids;
use App\Models\ModuleHygiene;
use App\Models\ModulePhysicalActivity;
use App\Models\ModulePrimaryObligation;
use App\Models\ModuleShopping;
use App\Models\ModuleSexualActivity;
use App\Models\ModuleSocialDensity;
use App\Models\ModuleWork;
use App\Models\ModuleWeather;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CheckPresenceOfContentInJournalEntryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sets_has_content_to_false_when_all_fields_are_null(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => true,
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertFalse($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_work_module_has_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
        ]);
        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'worked' => 'yes',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_physical_activity_module_has_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
        ]);
        ModulePhysicalActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_done_physical_activity' => 'yes',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_sexual_activity_module_has_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
        ]);
        ModuleSexualActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'had_sexual_activity' => 'yes',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_kids_module_has_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
        ]);
        ModuleKids::factory()->create([
            'journal_entry_id' => $entry->id,
            'had_kids_today' => 'yes',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_primary_obligation_module_has_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
        ]);
        ModulePrimaryObligation::factory()->create([
            'journal_entry_id' => $entry->id,
            'primary_obligation' => 'work',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_hygiene_module_has_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
        ]);
        ModuleHygiene::factory()->create([
            'journal_entry_id' => $entry->id,
            'showered' => 'yes',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_social_density_module_has_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
        ]);
        ModuleSocialDensity::factory()->create([
            'journal_entry_id' => $entry->id,
            'social_density' => 'crowd',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_shopping_module_has_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
        ]);
        ModuleShopping::factory()->create([
            'journal_entry_id' => $entry->id,
            'shopping_for' => 'for_self',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_weather_module_has_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
        ]);
        ModuleWeather::factory()->create([
            'journal_entry_id' => $entry->id,
            'condition' => 'rain',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_notes_exist(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
            'notes' => '<p>Today was great!</p>',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_false_when_notes_are_empty(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => true,
            'notes' => '',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertFalse($entry->has_content);
    }
}
