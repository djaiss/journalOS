<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Settings;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalModulesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_toggles_sleep_module_from_enabled_to_disabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create([
            'show_sleep_module' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => '1',
        ]);

        $response->assertOk();
        $response->assertSeeText('Modules');
        $response->assertSeeText('Disabled');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_sleep_module' => false,
        ]);
    }

    #[Test]
    public function it_toggles_sleep_module_from_disabled_to_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create([
            'show_sleep_module' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => '1',
        ]);

        $response->assertOk();
        $response->assertSeeText('Modules');
        $response->assertSeeText('Enabled');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_sleep_module' => true,
        ]);
    }

    #[Test]
    public function it_returns_not_found_if_user_does_not_own_the_journal(): void
    {
        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        $response = $this->actingAs($user)->put('/journals/' . $otherJournal->slug . '/settings/modules', [
            'module' => '1',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_requires_module_parameter(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules');

        $response->assertSessionHasErrors('module');
    }
}
