<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs\Api;

use App\Jobs\RecordMarketingPageVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_journal_entry_api_docs_page(): void
    {
        Queue::fake();

        $response = $this->get(route('marketing.docs.api.journal-entries', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.api.entries.journal-entry');

        Queue::assertPushedOn('low', RecordMarketingPageVisit::class, function (RecordMarketingPageVisit $job): bool {
            return $job->viewName === 'marketing.docs.api.entries.journal-entry';
        });
    }
}
