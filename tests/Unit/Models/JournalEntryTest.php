<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleHealth;
use App\Models\ModuleHygiene;
use App\Models\ModuleDayType;
use App\Models\ModuleEnergy;
use App\Models\ModulePhysicalActivity;
use App\Models\ModuleShopping;
use App\Models\ModuleSexualActivity;
use App\Models\ModuleSleep;
use App\Models\ModuleTravel;
use App\Models\ModuleWork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_journal(): void
    {
        $journal = Journal::factory()->create();
        $journalEntry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->assertTrue($journalEntry->journal()->exists());
    }

    #[Test]
    public function it_gets_the_date(): void
    {
        $journalEntry = JournalEntry::factory()->create([
            'day' => 1,
            'month' => 1,
            'year' => 2021,
        ]);

        $this->assertEquals('Friday January 1st, 2021', $journalEntry->getDate());
    }

    #[Test]
    public function it_has_many_books(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $book1 = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $book2 = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        DB::table('book_journal_entry')->insert([
            [
                'book_id' => $book1->id,
                'journal_entry_id' => $entry->id,
                'status' => BookStatus::STARTED->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $book2->id,
                'journal_entry_id' => $entry->id,
                'status' => BookStatus::FINISHED->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->assertCount(2, $entry->books);
        $this->assertEquals(BookStatus::STARTED->value, $entry->books->first()->pivot->status);
        $this->assertEquals(BookStatus::FINISHED->value, $entry->books->last()->pivot->status);
    }

    #[Test]
    public function it_has_one_module_sleep(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleSleep = ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $this->assertTrue($entry->moduleSleep()->exists());
        $this->assertEquals($moduleSleep->id, $entry->moduleSleep->id);
        $this->assertEquals('22:00', $entry->moduleSleep->bedtime);
        $this->assertEquals('06:00', $entry->moduleSleep->wake_up_time);
    }

    #[Test]
    public function it_has_one_module_energy(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleEnergy = ModuleEnergy::factory()->create([
            'journal_entry_id' => $entry->id,
            'energy' => 'normal',
        ]);

        $this->assertTrue($entry->moduleEnergy()->exists());
        $this->assertEquals($moduleEnergy->id, $entry->moduleEnergy->id);
        $this->assertEquals('normal', $entry->moduleEnergy->energy);
    }

    #[Test]
    public function it_has_one_module_health(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleHealth = ModuleHealth::factory()->create([
            'journal_entry_id' => $entry->id,
            'health' => 'okay',
        ]);

        $this->assertTrue($entry->moduleHealth()->exists());
        $this->assertEquals($moduleHealth->id, $entry->moduleHealth->id);
        $this->assertEquals('okay', $entry->moduleHealth->health);
    }

    #[Test]
    public function it_has_one_module_hygiene(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleHygiene = ModuleHygiene::factory()->create([
            'journal_entry_id' => $entry->id,
            'showered' => 'yes',
        ]);

        $this->assertTrue($entry->moduleHygiene()->exists());
        $this->assertEquals($moduleHygiene->id, $entry->moduleHygiene->id);
        $this->assertEquals('yes', $entry->moduleHygiene->showered);
    }

    #[Test]
    public function it_has_one_module_day_type(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleDayType = ModuleDayType::factory()->create([
            'journal_entry_id' => $entry->id,
            'day_type' => 'workday',
        ]);

        $this->assertTrue($entry->moduleDayType()->exists());
        $this->assertEquals($moduleDayType->id, $entry->moduleDayType->id);
        $this->assertEquals('workday', $entry->moduleDayType->day_type);
    }

    #[Test]
    public function it_has_one_module_travel(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleTravel = ModuleTravel::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_traveled_today' => 'yes',
            'travel_mode' => ['train'],
        ]);

        $this->assertTrue($entry->moduleTravel()->exists());
        $this->assertEquals($moduleTravel->id, $entry->moduleTravel->id);
        $this->assertEquals('yes', $entry->moduleTravel->has_traveled_today);
        $this->assertEquals(['train'], $entry->moduleTravel->travel_mode);
    }

    #[Test]
    public function it_has_one_module_shopping(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleShopping = ModuleShopping::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_shopped_today' => 'yes',
            'shopping_type' => ['groceries'],
        ]);

        $this->assertTrue($entry->moduleShopping()->exists());
        $this->assertEquals($moduleShopping->id, $entry->moduleShopping->id);
        $this->assertEquals('yes', $entry->moduleShopping->has_shopped_today);
        $this->assertEquals(['groceries'], $entry->moduleShopping->shopping_type);
    }

    #[Test]
    public function it_has_one_module_physical_activity(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $modulePhysicalActivity = ModulePhysicalActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_done_physical_activity' => 'yes',
            'activity_type' => 'running',
        ]);

        $this->assertTrue($entry->modulePhysicalActivity()->exists());
        $this->assertEquals($modulePhysicalActivity->id, $entry->modulePhysicalActivity->id);
        $this->assertEquals('yes', $entry->modulePhysicalActivity->has_done_physical_activity);
        $this->assertEquals('running', $entry->modulePhysicalActivity->activity_type);
    }

    #[Test]
    public function it_has_one_module_work(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $moduleWork = ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'worked' => 'yes',
            'work_mode' => 'remote',
        ]);

        $this->assertTrue($entry->moduleWork()->exists());
        $this->assertEquals($moduleWork->id, $entry->moduleWork->id);
        $this->assertEquals('yes', $entry->moduleWork->worked);
        $this->assertEquals('remote', $entry->moduleWork->work_mode);
    }

    #[Test]
    public function it_has_one_module_sexual_activity(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleSexualActivity = ModuleSexualActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'had_sexual_activity' => 'yes',
            'sexual_activity_type' => 'solo',
        ]);

        $this->assertTrue($entry->moduleSexualActivity()->exists());
        $this->assertEquals($moduleSexualActivity->id, $entry->moduleSexualActivity->id);
        $this->assertEquals('yes', $entry->moduleSexualActivity->had_sexual_activity);
        $this->assertEquals('solo', $entry->moduleSexualActivity->sexual_activity_type);
    }
}
