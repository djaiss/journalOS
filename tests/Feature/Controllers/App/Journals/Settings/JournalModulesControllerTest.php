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
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_sleep_module' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => '<b>sleep</b>',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_sleep_module' => false,
        ]);
    }

    #[Test]
    public function it_toggles_sleep_module_from_disabled_to_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_sleep_module' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'sleep',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

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
            'module' => 'sleep',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_rejects_module_names_longer_than_255_characters(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from('/journals/' . $journal->slug . '/settings/modules')
            ->put('/journals/' . $journal->slug . '/settings/modules', [
                'module' => str_repeat('a', 256),
            ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');
        $response->assertSessionHasErrors(['module']);
    }

    #[Test]
    public function it_toggles_travel_module_from_enabled_to_disabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_travel_module' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'travel',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_travel_module' => false,
        ]);
    }

    #[Test]
    public function it_toggles_travel_module_from_disabled_to_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_travel_module' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'travel',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_travel_module' => true,
        ]);
    }

    #[Test]
    public function it_toggles_day_type_module_from_enabled_to_disabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_day_type_module' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'day_type',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_day_type_module' => false,
        ]);
    }

    #[Test]
    public function it_toggles_day_type_module_from_disabled_to_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_day_type_module' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'day_type',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_day_type_module' => true,
        ]);
    }

    #[Test]
    public function it_toggles_primary_obligation_module_from_enabled_to_disabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_primary_obligation_module' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'primary_obligation',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_primary_obligation_module' => false,
        ]);
    }

    #[Test]
    public function it_toggles_primary_obligation_module_from_disabled_to_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_primary_obligation_module' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'primary_obligation',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_primary_obligation_module' => true,
        ]);
    }

    #[Test]
    public function it_toggles_health_module_from_enabled_to_disabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_health_module' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'health',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_health_module' => false,
        ]);
    }

    #[Test]
    public function it_toggles_health_module_from_disabled_to_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_health_module' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'health',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_health_module' => true,
        ]);
    }

    #[Test]
    public function it_toggles_mood_module_from_enabled_to_disabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_mood_module' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'mood',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_mood_module' => false,
        ]);
    }

    #[Test]
    public function it_toggles_mood_module_from_disabled_to_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_mood_module' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'mood',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_mood_module' => true,
        ]);
    }

    #[Test]
    public function it_toggles_energy_module_from_enabled_to_disabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_energy_module' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'energy',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_energy_module' => false,
        ]);
    }

    #[Test]
    public function it_toggles_energy_module_from_disabled_to_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_energy_module' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'energy',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_energy_module' => true,
        ]);
    }

    #[Test]
    public function it_toggles_sexual_activity_module_from_enabled_to_disabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_sexual_activity_module' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'sexual_activity',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_sexual_activity_module' => false,
        ]);
    }

    #[Test]
    public function it_toggles_sexual_activity_module_from_disabled_to_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_sexual_activity_module' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/modules', [
            'module' => 'sexual_activity',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_sexual_activity_module' => true,
        ]);
    }
}
