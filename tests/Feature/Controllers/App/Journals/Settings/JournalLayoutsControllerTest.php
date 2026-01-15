<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Settings;

use App\Models\Journal;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalLayoutsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_layout_for_the_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/journals/' . $journal->slug . '/settings/layouts', [
            'name' => 'Daily Overview',
            'columns_count' => 3,
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseHas('layouts', [
            'journal_id' => $journal->id,
            'columns_count' => 3,
            'is_active' => 0,
        ]);
    }

    #[Test]
    public function it_deletes_a_layout_for_the_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $response = $this->actingAs($user)->delete('/journals/' . $journal->slug . '/settings/layouts/' . $layout->id);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');

        $this->assertDatabaseMissing('layouts', [
            'id' => $layout->id,
        ]);
    }

    #[Test]
    public function it_returns_not_found_when_layout_is_not_in_the_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $otherJournal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $otherJournal->id,
        ]);

        $response = $this->actingAs($user)->delete('/journals/' . $journal->slug . '/settings/layouts/' . $layout->id);

        $response->assertNotFound();
    }

    #[Test]
    public function it_rejects_invalid_columns_count(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from('/journals/' . $journal->slug . '/settings/modules')
            ->post('/journals/' . $journal->slug . '/settings/layouts', [
                'name' => 'Invalid Layout',
                'columns_count' => 5,
            ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/modules');
        $response->assertSessionHasErrors(['columns_count']);
    }

    #[Test]
    public function it_returns_not_found_when_user_does_not_own_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        $response = $this->actingAs($user)->post('/journals/' . $journal->slug . '/settings/layouts', [
            'name' => 'Daily Overview',
            'columns_count' => 2,
        ]);

        $response->assertNotFound();
    }
}
