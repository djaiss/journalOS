<?php

declare(strict_types = 1);

namespace Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

final class GenerateMarketingSitemapTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up any existing sitemap
        if (File::exists(public_path('sitemap.xml'))) {
            File::delete(public_path('sitemap.xml'));
        }
    }

    protected function tearDown(): void
    {
        // Clean up after test
        if (File::exists(public_path('sitemap.xml'))) {
            File::delete(public_path('sitemap.xml'));
        }

        parent::tearDown();
    }

    public function test_it_generates_sitemap_file(): void
    {
        Artisan::call('marketing:generate-sitemap');

        static::assertFileExists(public_path('sitemap.xml'));
    }

    public function test_sitemap_contains_valid_xml(): void
    {
        Artisan::call('marketing:generate-sitemap');

        $content = File::get(public_path('sitemap.xml'));

        static::assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        static::assertStringContainsString('<urlset', $content);
        static::assertStringContainsString('</urlset>', $content);
    }

    public function test_command_outputs_success_message(): void
    {
        $this->artisan('marketing:generate-sitemap')
            ->expectsOutput('Marketing sitemap generated successfully at public/sitemap.xml')
            ->assertExitCode(0);
    }
}
