<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\ExtractMarketingTimestamps;
use Tests\TestCase;

final class ExtractMarketingTimestampsTest extends TestCase
{
    public function test_it_generates_config_content_correctly(): void
    {
        $command = new ExtractMarketingTimestamps;

        $pages = [
            'marketing/about' => '2024-01-15 10:30:00',
            'marketing/contact' => '2024-01-14 15:45:00',
            'marketing/docs/index' => '2024-01-16 08:20:00',
        ];

        $content = $command->generateConfigContent($pages);

        $this->assertStringContainsString("<?php", $content);
        $this->assertStringContainsString("return [", $content);
        $this->assertStringContainsString("'pages' => [", $content);
        $this->assertStringContainsString("'marketing/about' => '2024-01-15 10:30:00'", $content);
        $this->assertStringContainsString("'marketing/contact' => '2024-01-14 15:45:00'", $content);
        $this->assertStringContainsString("'marketing/docs/index' => '2024-01-16 08:20:00'", $content);
    }

    public function test_it_gets_file_modified_time(): void
    {
        $command = new ExtractMarketingTimestamps;
        $testFilePath = __FILE__;

        $modifiedTime = $command->getFileModifiedTime($testFilePath);

        $this->assertIsInt($modifiedTime);
        $this->assertGreaterThan(0, $modifiedTime);
    }
}
