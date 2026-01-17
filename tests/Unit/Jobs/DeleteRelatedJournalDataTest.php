<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\DeleteRelatedJournalData;
use App\Models\Book;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Log;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\ModuleDayType;
use App\Models\ModuleEnergy;
use App\Models\ModuleHealth;
use App\Models\ModuleHygiene;
use App\Models\ModuleKids;
use App\Models\ModuleMood;
use App\Models\ModulePhysicalActivity;
use App\Models\ModulePrimaryObligation;
use App\Models\ModuleSocialDensity;
use App\Models\ModuleSexualActivity;
use App\Models\ModuleShopping;
use App\Models\ModuleSleep;
use App\Models\ModuleTravel;
use App\Models\ModuleWeather;
use App\Models\ModuleWork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DeleteRelatedJournalDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_journal_entries_and_all_related_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $layoutModule = LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'sleep',
            'column_number' => 1,
            'position' => 1,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleEnergy::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleTravel::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleWeather::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleHealth::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleHygiene::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleMood::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleDayType::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModulePhysicalActivity::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleSexualActivity::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleKids::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModulePrimaryObligation::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleSocialDensity::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);
        ModuleShopping::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry->books()->attach($book, ['status' => 'reading']);

        $job = new DeleteRelatedJournalData($journal->id);
        $job->handle();

        $this->assertDatabaseMissing('journal_entries', [
            'id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('layouts', [
            'id' => $layout->id,
        ]);
        $this->assertDatabaseMissing('layout_modules', [
            'id' => $layoutModule->id,
        ]);
        $this->assertDatabaseMissing('module_sleep', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_energy', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_work', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_travel', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_weather', [
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
        $this->assertDatabaseMissing('module_day_type', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_physical_activity', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_sexual_activity', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_kids', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_primary_obligation', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_social_density', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('book_journal_entry', [
            'journal_entry_id' => $entry->id,
        ]);
        $this->assertDatabaseMissing('module_shopping', [
            'journal_entry_id' => $entry->id,
        ]);
    }

    #[Test]
    public function it_deletes_logs_for_the_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'journal_id' => $journal->id,
        ]);

        $job = new DeleteRelatedJournalData($journal->id);
        $job->handle();

        $this->assertDatabaseMissing('logs', [
            'id' => $log->id,
        ]);
    }

    #[Test]
    public function it_handles_multiple_journal_entries(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $entries = JournalEntry::factory()->count(5)->create([
            'journal_id' => $journal->id,
        ]);

        foreach ($entries as $entry) {
            ModuleSleep::factory()->create([
                'journal_entry_id' => $entry->id,
            ]);
            ModuleEnergy::factory()->create([
                'journal_entry_id' => $entry->id,
            ]);
            ModuleWork::factory()->create([
                'journal_entry_id' => $entry->id,
            ]);
            ModuleWeather::factory()->create([
                'journal_entry_id' => $entry->id,
            ]);
            ModuleHygiene::factory()->create([
                'journal_entry_id' => $entry->id,
            ]);
            ModuleSexualActivity::factory()->create([
                'journal_entry_id' => $entry->id,
            ]);
            ModuleKids::factory()->create([
                'journal_entry_id' => $entry->id,
            ]);
            ModulePrimaryObligation::factory()->create([
                'journal_entry_id' => $entry->id,
            ]);
            ModuleSocialDensity::factory()->create([
                'journal_entry_id' => $entry->id,
            ]);
        }

        $job = new DeleteRelatedJournalData($journal->id);
        $job->handle();

        foreach ($entries as $entry) {
            $this->assertDatabaseMissing('journal_entries', [
                'id' => $entry->id,
            ]);
            $this->assertDatabaseMissing('module_sleep', [
                'journal_entry_id' => $entry->id,
            ]);
            $this->assertDatabaseMissing('module_energy', [
                'journal_entry_id' => $entry->id,
            ]);
            $this->assertDatabaseMissing('module_work', [
                'journal_entry_id' => $entry->id,
            ]);
            $this->assertDatabaseMissing('module_weather', [
                'journal_entry_id' => $entry->id,
            ]);
            $this->assertDatabaseMissing('module_hygiene', [
                'journal_entry_id' => $entry->id,
            ]);
            $this->assertDatabaseMissing('module_sexual_activity', [
                'journal_entry_id' => $entry->id,
            ]);
            $this->assertDatabaseMissing('module_kids', [
                'journal_entry_id' => $entry->id,
            ]);
            $this->assertDatabaseMissing('module_primary_obligation', [
                'journal_entry_id' => $entry->id,
            ]);
            $this->assertDatabaseMissing('module_social_density', [
                'journal_entry_id' => $entry->id,
            ]);
        }
    }

    #[Test]
    public function it_does_not_delete_data_from_other_journals(): void
    {
        $user = User::factory()->create();
        $journal1 = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $journal2 = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout1 = Layout::factory()->create([
            'journal_id' => $journal1->id,
        ]);
        $layout2 = Layout::factory()->create([
            'journal_id' => $journal2->id,
        ]);
        $layoutModule1 = LayoutModule::factory()->create([
            'layout_id' => $layout1->id,
            'module_key' => 'sleep',
            'column_number' => 1,
            'position' => 1,
        ]);
        $layoutModule2 = LayoutModule::factory()->create([
            'layout_id' => $layout2->id,
            'module_key' => 'sleep',
            'column_number' => 1,
            'position' => 1,
        ]);

        $entry1 = JournalEntry::factory()->create([
            'journal_id' => $journal1->id,
        ]);
        $entry2 = JournalEntry::factory()->create([
            'journal_id' => $journal2->id,
        ]);

        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry1->id,
        ]);
        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry2->id,
        ]);
        ModuleEnergy::factory()->create([
            'journal_entry_id' => $entry1->id,
        ]);
        ModuleEnergy::factory()->create([
            'journal_entry_id' => $entry2->id,
        ]);
        ModuleWeather::factory()->create([
            'journal_entry_id' => $entry1->id,
        ]);
        ModuleWeather::factory()->create([
            'journal_entry_id' => $entry2->id,
        ]);
        ModuleHygiene::factory()->create([
            'journal_entry_id' => $entry1->id,
        ]);
        ModuleHygiene::factory()->create([
            'journal_entry_id' => $entry2->id,
        ]);
        ModuleSexualActivity::factory()->create([
            'journal_entry_id' => $entry1->id,
        ]);
        ModuleSexualActivity::factory()->create([
            'journal_entry_id' => $entry2->id,
        ]);
        ModuleKids::factory()->create([
            'journal_entry_id' => $entry1->id,
        ]);
        ModuleKids::factory()->create([
            'journal_entry_id' => $entry2->id,
        ]);
        ModulePrimaryObligation::factory()->create([
            'journal_entry_id' => $entry1->id,
        ]);
        ModulePrimaryObligation::factory()->create([
            'journal_entry_id' => $entry2->id,
        ]);
        ModuleSocialDensity::factory()->create([
            'journal_entry_id' => $entry1->id,
        ]);
        ModuleSocialDensity::factory()->create([
            'journal_entry_id' => $entry2->id,
        ]);

        $log1 = Log::factory()->create([
            'user_id' => $user->id,
            'journal_id' => $journal1->id,
        ]);
        $log2 = Log::factory()->create([
            'user_id' => $user->id,
            'journal_id' => $journal2->id,
        ]);

        $job = new DeleteRelatedJournalData($journal1->id);
        $job->handle();

        $this->assertDatabaseMissing('journal_entries', [
            'id' => $entry1->id,
        ]);
        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry2->id,
        ]);
        $this->assertDatabaseMissing('layouts', [
            'id' => $layout1->id,
        ]);
        $this->assertDatabaseHas('layouts', [
            'id' => $layout2->id,
        ]);
        $this->assertDatabaseMissing('layout_modules', [
            'id' => $layoutModule1->id,
        ]);
        $this->assertDatabaseHas('layout_modules', [
            'id' => $layoutModule2->id,
        ]);
        $this->assertDatabaseMissing('module_sleep', [
            'journal_entry_id' => $entry1->id,
        ]);
        $this->assertDatabaseHas('module_sleep', [
            'journal_entry_id' => $entry2->id,
        ]);
        $this->assertDatabaseMissing('module_energy', [
            'journal_entry_id' => $entry1->id,
        ]);
        $this->assertDatabaseHas('module_energy', [
            'journal_entry_id' => $entry2->id,
        ]);
        $this->assertDatabaseMissing('module_weather', [
            'journal_entry_id' => $entry1->id,
        ]);
        $this->assertDatabaseHas('module_weather', [
            'journal_entry_id' => $entry2->id,
        ]);
        $this->assertDatabaseMissing('module_hygiene', [
            'journal_entry_id' => $entry1->id,
        ]);
        $this->assertDatabaseHas('module_hygiene', [
            'journal_entry_id' => $entry2->id,
        ]);
        $this->assertDatabaseMissing('module_sexual_activity', [
            'journal_entry_id' => $entry1->id,
        ]);
        $this->assertDatabaseHas('module_sexual_activity', [
            'journal_entry_id' => $entry2->id,
        ]);
        $this->assertDatabaseMissing('module_kids', [
            'journal_entry_id' => $entry1->id,
        ]);
        $this->assertDatabaseHas('module_kids', [
            'journal_entry_id' => $entry2->id,
        ]);
        $this->assertDatabaseMissing('module_primary_obligation', [
            'journal_entry_id' => $entry1->id,
        ]);
        $this->assertDatabaseHas('module_primary_obligation', [
            'journal_entry_id' => $entry2->id,
        ]);
        $this->assertDatabaseMissing('module_social_density', [
            'journal_entry_id' => $entry1->id,
        ]);
        $this->assertDatabaseHas('module_social_density', [
            'journal_entry_id' => $entry2->id,
        ]);
        $this->assertDatabaseMissing('logs', [
            'id' => $log1->id,
        ]);
        $this->assertDatabaseHas('logs', [
            'id' => $log2->id,
        ]);
    }
}
