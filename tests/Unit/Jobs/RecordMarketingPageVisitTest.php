<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\RecordMarketingPageVisit;
use App\Models\MarketingPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class RecordMarketingPageVisitTest extends TestCase
{
    use RefreshDatabase;


    public function it_creates_a_marketing_page_when_it_does_not_exist(): void
    {
        $this->assertDatabaseMissing('marketing_pages', ['url' => 'pricing']);

        $job = new RecordMarketingPageVisit(viewName: 'pricing');
        $job->handle();

        $this->assertDatabaseHas('marketing_pages', ['url' => 'pricing']);
    }

    #[Test]
    public function it_records_an_existing_marketing_page_visit(): void
    {
        $marketingPage = MarketingPage::factory()->create([
            'url' => 'features',
            'pageviews' => 0,
        ]);

        $job = new RecordMarketingPageVisit(viewName: 'features');
        $job->handle();

        $marketingPage->refresh();
        $this->assertEquals('features', $marketingPage->url);
        $this->assertEquals(1, $marketingPage->pageviews);
    }
}
