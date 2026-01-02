<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/**
 * This command generates a sitemap for all marketing routes.
 */
final class GenerateMarketingSitemap extends Command
{
    protected $signature = 'marketing:generate-sitemap';

    protected $description = 'Generate sitemap for marketing routes';

    public function handle(): void
    {
        $sitemap = Sitemap::create();

        $marketingRoutes = $this->getMarketingRoutes();

        foreach ($marketingRoutes as $route) {
            $url = Url::create($route['url'])
                ->setLastModificationDate($route['lastModified']);

            // Set homepage to daily frequency and priority 1
            if ($route['routeName'] === 'marketing.index') {
                $url->setChangeFrequency('daily')->setPriority(1.0);
            } else {
                $url->setChangeFrequency('weekly')->setPriority(0.8);
            }

            $sitemap->add($url);
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Marketing sitemap generated successfully at public/sitemap.xml');
    }

    /**
     * Get all marketing routes with their URLs and last modification dates.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getMarketingRoutes(): array
    {
        $routes = [];
        $marketingTimestamps = config('marketing-timestamps.pages', []);

        // Get all routes that use the marketing middleware
        $routeCollection = Route::getRoutes();
        $baseUrl = 'https://journalos.cloud';

        foreach ($routeCollection->getRoutes() as $route) {
            $middleware = $route->middleware();

            // Check if route uses marketing middleware
            if (in_array('marketing', $middleware)) {
                $routeName = $route->getName();

                if ($routeName) {
                    $url = $baseUrl . '/' . mb_ltrim((string) $route->uri(), '/');

                    // Get last modification date from config or use current date
                    $lastModified = $marketingTimestamps[$routeName] ?? now()->toDateTimeString();

                    $routes[] = [
                        'url' => $url,
                        'lastModified' => \Illuminate\Support\Facades\Date::parse($lastModified),
                        'routeName' => $routeName,
                    ];
                }
            }
        }

        return $routes;
    }
}
