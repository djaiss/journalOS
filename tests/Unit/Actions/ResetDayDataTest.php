<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ResetDayData;
use App\Enums\BookStatus;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleDayType;
use App\Models\ModuleEnergy;
use App\Models\ModuleHealth;
use App\Models\ModuleHygiene;
use App\Models\ModuleKids;
use App\Models\ModuleMood;
use App\Models\ModulePhysicalActivity;
use App\Models\ModulePrimaryObligation;
use App\Models\ModuleSexualActivity;
use App\Models\ModuleShopping;
use App\Models\ModuleSleep;
use App\Models\ModuleSocialDensity;
use App\Models\ModuleTravel;
use App\Models\ModuleWork;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ResetDayDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_resets_all_day_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'notes' => '<p>Notes for today.</p>',
        ]);

        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '21:30',
            'wake_up_time' => '06:30',
            'sleep_duration_in_minutes' => '540',
        ]);
        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'worked' => 'yes',
            'work_mode' => 'remote',
            'work_load' => 'heavy',
            'work_procrastinated' => 'no',
        ]);
        ModuleTravel::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_traveled_today' => 'yes',
            'travel_details' => 'Commute',
            'travel_mode' => ['car'],
        ]);
        ModuleShopping::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_shopped_today' => 'yes',
            'shopping_type' => ['books_media'],
            'shopping_intent' => 'planned',
            'shopping_context' => 'alone',
            'shopping_for' => 'for_self',
        ]);
        ModuleKids::factory()->create([
            'journal_entry_id' => $entry->id,
            'had_kids_today' => 'yes',
        ]);
        ModuleDayType::factory()->create([
            'journal_entry_id' => $entry->id,
            'day_type' => 'normal',
        ]);
        ModulePrimaryObligation::factory()->create([
            'journal_entry_id' => $entry->id,
            'primary_obligation' => 'work',
        ]);
        ModulePhysicalActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_done_physical_activity' => 'yes',
            'activity_type' => 'running',
            'activity_intensity' => 'moderate',
        ]);
        ModuleHealth::factory()->create([
            'journal_entry_id' => $entry->id,
            'health' => 'great',
        ]);
        ModuleHygiene::factory()->create([
            'journal_entry_id' => $entry->id,
            'showered' => 'yes',
            'brushed_teeth' => 'yes',
            'skincare' => 'yes',
        ]);
        ModuleMood::factory()->create([
            'journal_entry_id' => $entry->id,
            'mood' => 'happy',
        ]);
        ModuleSexualActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'had_sexual_activity' => 'yes',
            'sexual_activity_type' => ['intimacy'],
        ]);
        ModuleEnergy::factory()->create([
            'journal_entry_id' => $entry->id,
            'energy' => 'high',
        ]);
        ModuleSocialDensity::factory()->create([
            'journal_entry_id' => $entry->id,
            'social_density' => 'full',
        ]);
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry->books()->attach($book->id, [
            'status' => BookStatus::STARTED->value,
        ]);

        $result = (new ResetDayData(
            user: $user,
            entry: $entry,
        ))->execute();

        $entry->refresh();

        $this->assertNull($result->moduleSleep);
        $this->assertNull($result->moduleWork);
        $this->assertNull($result->moduleTravel);
        $this->assertNull($result->moduleShopping);
        $this->assertNull($result->moduleKids);
        $this->assertNull($result->moduleDayType);
        $this->assertNull($result->modulePrimaryObligation);
        $this->assertNull($result->modulePhysicalActivity);
        $this->assertNull($result->moduleHealth);
        $this->assertNull($result->moduleHygiene);
        $this->assertNull($result->moduleMood);
        $this->assertNull($result->moduleSexualActivity);
        $this->assertNull($result->moduleEnergy);
        $this->assertNull($result->moduleSocialDensity);
        $this->assertSame('', mb_trim($entry->notes->toPlainText()));
        $this->assertCount(0, $entry->books);

        $this->assertDatabaseMissing('module_sleep', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_work', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_travel', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_shopping', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_kids', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_day_type', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_primary_obligation', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_physical_activity', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_health', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_hygiene', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_mood', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_sexual_activity', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_energy', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_social_density', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('book_journal_entry', [
            'journal_entry_id' => $entry->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'day_data_reset' && $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: CheckPresenceOfContentInJournalEntry::class,
            callback: function (CheckPresenceOfContentInJournalEntry $job) use ($entry): bool {
                return $job->entry->id === $entry->id;
            },
        );
    }

    #[Test]
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new ResetDayData(
            user: $user,
            entry: $entry,
        ))->execute();
    }
}
