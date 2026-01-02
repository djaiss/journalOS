<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * This command updates the last modified timestamps for all marketing pages.
 */
final class ExtractMarketingTimestamps extends Command
{
    protected $signature = 'marketing:extract-timestamps';

    protected $description = 'Update last modified timestamps for marketing pages';

    public function handle(): void
    {
        $this->updateTimestamps();
        $this->info('Marketing page timestamps updated.');
    }

    private function updateTimestamps(): void
    {
        $directoryPath = resource_path('views/marketing');
        $filesInfo = [];

        if (! File::isDirectory($directoryPath)) {
            return;
        }

        foreach (File::allFiles($directoryPath) as $file) {
            $relativePath = mb_rtrim(str_replace(
                [resource_path('views/'), '.blade.php', DIRECTORY_SEPARATOR],
                ['', '', '/'],
                $file->getPathname(),
            ), '.');

            $filesInfo[$relativePath] = date('Y-m-d H:i:s', $this->getFileModifiedTime($file->getRealPath()));
        }

        $content = $this->generateConfigContent($filesInfo);
        File::put(config_path('marketing-timestamps.php'), $content);
    }

    public function getFileModifiedTime(string $path): int
    {
        return filemtime($path);
    }

    public function generateConfigContent(array $pages): string
    {
        $output = "<?php\n\nreturn [\n    'pages' => [\n";
        foreach ($pages as $path => $timestamp) {
            $output .= "        '{$path}' => '{$timestamp}',\n";
        }

        return $output . "    ],\n];";
    }
}
