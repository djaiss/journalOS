<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\MarketingPage;

final class RecordMarketingPageVisit implements ShouldQueue
{
    use Queueable;

    private MarketingPage $marketingPage;

    /**
     * Record a marketing page visit.
     * The URL visited is passed as the view that is rendered.
     * To get the mapping of URLs to views, see routes/marketing.php.
     */
    public function __construct(
        public string $viewName,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->getMarketingPageObject();
        $this->incrementPageViews();
    }

    private function getMarketingPageObject(): void
    {
        $this->marketingPage = MarketingPage::query()->firstOrCreate([
            'url' => $this->viewName,
        ]);
    }

    private function incrementPageViews(): void
    {
        $this->marketingPage->increment('pageviews');
    }
}
