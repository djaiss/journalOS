<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs\Api\Modules;

use App\Jobs\RecordMarketingPageVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ShoppingControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_shopping_module_api_docs_page(): void
    {
        Queue::fake();

        $response = $this->get(route('marketing.docs.api.modules.shopping', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.api.modules.shopping');

        Queue::assertPushedOn('low', RecordMarketingPageVisit::class, function (RecordMarketingPageVisit $job): bool {
            return $job->viewName === 'marketing.docs.api.modules.shopping';
        });
    }
}
