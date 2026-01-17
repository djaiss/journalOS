<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateDefaultLayoutForJournal;
use App\Models\Journal;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateDefaultLayoutForJournalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_default_layout_with_three_columns(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        (new CreateDefaultLayoutForJournal($journal))->execute();

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->first();

        $this->assertNotNull($layout);
        $this->assertEquals('Default', $layout->name);
        $this->assertEquals(3, $layout->columns_count);
        $this->assertTrue($layout->is_active);
    }

    #[Test]
    public function it_adds_all_modules_except_sexual_activity_and_kids(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        (new CreateDefaultLayoutForJournal($journal))->execute();

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->first();

        $moduleKeys = LayoutModule::query()
            ->where('layout_id', $layout->id)
            ->pluck('module_key')
            ->toArray();

        $this->assertNotContains('sexual_activity', $moduleKeys);
        $this->assertNotContains('kids', $moduleKeys);

        $this->assertContains('health', $moduleKeys);
        $this->assertContains('hygiene', $moduleKeys);
        $this->assertContains('energy', $moduleKeys);
        $this->assertContains('physical_activity', $moduleKeys);
        $this->assertContains('sleep', $moduleKeys);
        $this->assertContains('mood', $moduleKeys);
        $this->assertContains('work', $moduleKeys);
        $this->assertContains('day_type', $moduleKeys);
        $this->assertContains('primary_obligation', $moduleKeys);
        $this->assertContains('shopping', $moduleKeys);
        $this->assertContains('travel', $moduleKeys);
        $this->assertContains('weather', $moduleKeys);
        $this->assertContains('social_density', $moduleKeys);
    }

    #[Test]
    public function it_organizes_modules_by_theme_in_three_columns(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        (new CreateDefaultLayoutForJournal($journal))->execute();

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->first();

        $column1Modules = LayoutModule::query()
            ->where('layout_id', $layout->id)
            ->where('column_number', 1)
            ->orderBy('position')
            ->pluck('module_key')
            ->toArray();

        $this->assertEquals(['sleep', 'energy', 'health', 'physical_activity', 'hygiene'], $column1Modules);

        $column2Modules = LayoutModule::query()
            ->where('layout_id', $layout->id)
            ->where('column_number', 2)
            ->orderBy('position')
            ->pluck('module_key')
            ->toArray();

        $this->assertEquals(['mood', 'work', 'day_type', 'primary_obligation', 'shopping'], $column2Modules);

        $column3Modules = LayoutModule::query()
            ->where('layout_id', $layout->id)
            ->where('column_number', 3)
            ->orderBy('position')
            ->pluck('module_key')
            ->toArray();

        $this->assertEquals(['travel', 'weather', 'social_density'], $column3Modules);
    }

    #[Test]
    public function it_assigns_correct_position_to_each_module(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        (new CreateDefaultLayoutForJournal($journal))->execute();

        $layout = Layout::query()
            ->where('journal_id', $journal->id)
            ->first();

        $sleepModule = LayoutModule::query()
            ->where('layout_id', $layout->id)
            ->where('module_key', 'sleep')
            ->first();

        $this->assertEquals(1, $sleepModule->column_number);
        $this->assertEquals(1, $sleepModule->position);

        $healthModule = LayoutModule::query()
            ->where('layout_id', $layout->id)
            ->where('module_key', 'health')
            ->first();

        $this->assertEquals(1, $healthModule->column_number);
        $this->assertEquals(3, $healthModule->position);

        $socialDensityModule = LayoutModule::query()
            ->where('layout_id', $layout->id)
            ->where('module_key', 'social_density')
            ->first();

        $this->assertEquals(3, $socialDensityModule->column_number);
        $this->assertEquals(3, $socialDensityModule->position);
    }
}
