<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Journals\Settings;

use App\Models\Journal;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalLayoutModulesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_layout_modules_page(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 2,
        ]);

        $response = $this->actingAs($user)->get(
            '/journals/' . $journal->slug . '/settings/layouts/' . $layout->id . '/modules',
        );

        $response->assertOk();
        $response->assertSee($layout->name);
    }

    #[Test]
    public function it_adds_a_module_to_a_layout_column(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 2,
        ]);

        $response = $this->actingAs($user)->post(
            '/journals/' . $journal->slug . '/settings/layouts/' . $layout->id . '/modules',
            [
                'module_key' => 'sleep',
                'column_number' => 2,
            ],
        );

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/layouts/' . $layout->id . '/modules');

        $this->assertDatabaseHas('layout_modules', [
            'layout_id' => $layout->id,
            'module_key' => 'sleep',
            'column_number' => 2,
            'position' => 1,
        ]);
    }

    #[Test]
    public function it_rejects_modules_that_already_exist_in_the_layout(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 2,
        ]);
        LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'sleep',
            'column_number' => 1,
            'position' => 1,
        ]);

        $response = $this->actingAs($user)
            ->from('/journals/' . $journal->slug . '/settings/layouts/' . $layout->id . '/modules')
            ->post('/journals/' . $journal->slug . '/settings/layouts/' . $layout->id . '/modules', [
                'module_key' => 'sleep',
                'column_number' => 2,
            ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/layouts/' . $layout->id . '/modules');
        $response->assertSessionHasErrors(['module_key']);
    }

    #[Test]
    public function it_removes_a_module_from_a_layout(): void
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

        $response = $this->actingAs($user)->delete(
            '/journals/'
            . $journal->slug
            . '/settings/layouts/'
            . $layout->id
            . '/modules/'
            . $layoutModule->module_key,
        );

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/layouts/' . $layout->id . '/modules');

        $this->assertDatabaseMissing('layout_modules', [
            'id' => $layoutModule->id,
        ]);
    }

    #[Test]
    public function it_reorders_a_module_in_a_layout(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 2,
        ]);
        $firstModule = LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'sleep',
            'column_number' => 1,
            'position' => 1,
        ]);
        $secondModule = LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'work',
            'column_number' => 1,
            'position' => 2,
        ]);

        $response = $this->actingAs($user)->put(
            '/journals/' . $journal->slug . '/settings/layouts/' . $layout->id . '/modules/reorder',
            [
                'module_key' => $secondModule->module_key,
                'column_number' => 1,
                'position' => 1,
            ],
        );

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/layouts/' . $layout->id . '/modules');

        $this->assertSame(2, $firstModule->refresh()->position);
        $this->assertSame(1, $secondModule->refresh()->position);
    }
}
