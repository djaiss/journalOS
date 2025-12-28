<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Settings;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalSettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_journal_settings_page(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create([
            'name' => 'Dunder Mifflin',
        ]);

        $response = $this->actingAs($user)->get('/journals/' . $journal->slug . '/settings');

        $response->assertOk();
        $response->assertSeeText('Journal settings');
        $response->assertSee('Dunder Mifflin');
        $response->assertViewHas('journal', fn($viewJournal): bool => $viewJournal->id === $journal->id);
    }

    #[Test]
    public function it_updates_the_journal_name(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create([
            'name' => 'Dunder Mifflin',
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings', [
            'journal_name' => 'Threat Level Midnight',
        ]);

        $journal->refresh();

        $response->assertRedirect(route('journal.settings.show', ['slug' => $journal->slug]));
        $response->assertSessionHas('status', 'Journal renamed successfully');
        $this->assertEquals('Threat Level Midnight', $journal->name);
    }

    #[Test]
    public function it_validates_the_journal_name_when_updating(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();

        $response = $this->actingAs($user)
            ->from('/journals/' . $journal->slug . '/settings')
            ->put('/journals/' . $journal->slug . '/settings', [
                'journal_name' => 'Invalid@!',
            ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings');
        $response->assertSessionHasErrors(['journal_name']);
    }

    #[Test]
    public function it_returns_not_found_if_user_does_not_own_the_journal(): void
    {
        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        $response = $this->actingAs($user)->get('/journals/' . $otherJournal->slug . '/settings');

        $response->assertNotFound();
    }

    #[Test]
    public function it_returns_not_found_when_trying_to_update_a_journal_the_user_does_not_own(): void
    {
        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        $response = $this->actingAs($user)->put('/journals/' . $otherJournal->slug . '/settings', [
            'journal_name' => 'Threat Level Midnight',
        ]);

        $response->assertNotFound();
    }
}
