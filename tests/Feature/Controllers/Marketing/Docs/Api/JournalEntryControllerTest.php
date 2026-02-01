<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Marketing\Docs\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_journal_entry_api_docs_page(): void
    {
        $response = $this->get(route('marketing.docs.api.journal-entries', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.api.entries.journal-entry');
    }
}
