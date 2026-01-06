<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_journals_of_the_user(): void
    {
        $user = User::factory()->create();
        Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Dunder Mifflin',
            'slug' => 'dunder-mifflin',
        ]);

        $response = $this->actingAs($user)->get('/journals');

        $response->assertStatus(200);
        $response->assertSee('Dunder Mifflin');
    }

    #[Test]
    public function it_shows_a_message_when_the_user_doesnt_have_journals(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/journals');

        $response->assertStatus(200);
        $response->assertSee('You do not have any journals yet.');
    }

    #[Test]
    public function it_creates_a_journal(): void
    {
        $user = User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
            'password' => Hash::make('5UTHSmdj'),
        ]);

        $response = $this->actingAs($user)->get('/journals/create');

        $response = $this->post('/journals', [
            'journal_name' => 'Dunder Mifflin',
        ]);

        $journal = Journal::latest()->first();

        $response->assertRedirect('/journals/' . $journal->slug);
    }

    #[Test]
    public function it_lets_an_user_access_a_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/journals/' . $journal->slug);

        $response->assertStatus(302);
    }

    #[Test]
    public function it_does_not_let_an_user_access_a_journal_he_has_no_access_to(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        $response = $this->actingAs($user)->get('/journals/' . $journal->slug);

        $response->assertStatus(404);
    }

    #[Test]
    public function it_updates_the_journal_name(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create([
            'name' => 'Dunder Mifflin',
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug, [
            'journal_name' => 'Threat Level Midnight',
        ]);

        $journal->refresh();

        $response->assertRedirect(route('journal.settings.management.index', ['slug' => $journal->slug]));
        $response->assertSessionHas('status', 'Changes saved');
        $this->assertEquals('Threat Level Midnight', $journal->name);
    }

    #[Test]
    public function it_validates_the_journal_name_when_updating(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();

        $response = $this->actingAs($user)
            ->from('/journals/' . $journal->slug . '/settings/management')
            ->put('/journals/' . $journal->slug, [
                'journal_name' => 'Invalid@!',
            ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/management');
        $response->assertSessionHasErrors(['journal_name']);
    }

    #[Test]
    public function it_returns_not_found_when_trying_to_update_a_journal_the_user_does_not_own(): void
    {
        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        $response = $this->actingAs($user)->put('/journals/' . $otherJournal->slug, [
            'journal_name' => 'Threat Level Midnight',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_lets_a_user_destroy_their_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete('/journals/' . $journal->slug);

        $response->assertRedirect('/journals');

        $this->assertDatabaseMissing('journals', [
            'id' => $journal->id,
        ]);
    }

    #[Test]
    public function it_does_not_let_a_user_destroy_a_journal_they_do_not_own(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        $response = $this->actingAs($user)->delete('/journals/' . $journal->slug);

        $response->assertStatus(404);

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
        ]);
    }
}
