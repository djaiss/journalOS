<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class JournalControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_journals_of_the_user(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
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

        $response->assertStatus(200);
    }

    #[Test]
    public function it_does_not_let_an_user_access_a_journal_he_has_no_access_to(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        $response = $this->actingAs($user)->get('/journals/' . $journal->slug);

        $response->assertStatus(404);
    }
}
