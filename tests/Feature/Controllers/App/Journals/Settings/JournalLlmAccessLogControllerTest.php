<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Settings;

use App\Models\Journal;
use App\Models\JournalLlmAccessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalLlmAccessLogControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_llm_access_logs_for_journal_owner(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        JournalLlmAccessLog::factory()->create([
            'journal_id' => $journal->id,
            'requested_year' => 2026,
            'requested_month' => 1,
            'requested_day' => 27,
            'request_url' => 'https://journalos.test/llm/llm-test-key/2026/1/27',
        ]);

        $response = $this->actingAs($user)->get('/journals/' . $journal->slug . '/settings/llm/logs');

        $response->assertOk();
        $response->assertSeeText('2026-01-27');
        $response->assertSeeText('https://journalos.test/llm/llm-test-key/2026/1/27');
    }

    #[Test]
    public function it_returns_not_found_for_non_owner(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        $response = $this->actingAs($user)->get('/journals/' . $journal->slug . '/settings/llm/logs');

        $response->assertNotFound();
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $journal = Journal::factory()->create();

        $response = $this->get('/journals/' . $journal->slug . '/settings/llm/logs');

        $response->assertRedirect('/login');
    }
}
