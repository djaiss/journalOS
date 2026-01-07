<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Settings;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalManagementSettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_journal_management_settings_page(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Dunder Mifflin',
        ]);

        $response = $this->actingAs($user)->get('/journals/' . $journal->slug . '/settings/management');

        $response->assertOk();
        $response->assertSeeText('Maintenance');
        $response->assertSeeText('Rename journal');
        $response->assertSeeText('Delete journal');
        $response->assertSeeText('Dunder Mifflin');
        $response->assertViewHas('journal', fn($viewJournal): bool => $viewJournal->id === $journal->id);
    }

    #[Test]
    public function it_returns_not_found_if_user_does_not_own_the_journal(): void
    {
        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        $response = $this->actingAs($user)->get('/journals/' . $otherJournal->slug . '/settings/management');

        $response->assertNotFound();
    }
}
