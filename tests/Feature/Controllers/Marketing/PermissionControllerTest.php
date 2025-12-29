<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing;

use App\Jobs\RecordMarketingPageVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_permissions_concepts_page(): void
    {
        Queue::fake();

        $response = $this->get(route('marketing.docs.concepts.permissions', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.concepts.permissions');

        Queue::assertPushedOn('low', RecordMarketingPageVisit::class, function (RecordMarketingPageVisit $job): bool {
            return $job->viewName === 'marketing.docs.concepts.permissions';
        });
    }
}
