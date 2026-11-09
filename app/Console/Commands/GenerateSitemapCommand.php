<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--category= : Generate business sitemap for a specific category ID only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and update the central XML sitemap index and sub-sitemaps';

    /**
     * Execute the console command.
     */
    public function handle(SitemapService $sitemapService): int
    {
        $categoryId = $this->option('category');

        if ($categoryId) {
            $this->info("Generating business sitemaps for primary category ID {$categoryId}...");
            $files = $sitemapService->generateCategoryBusinessSitemaps((int) $categoryId);
            $indexFile = $sitemapService->updateIndex();

            $this->info("Generated " . count($files) . " category sitemap file(s):");
            foreach ($files as $file) {
                $this->line(" - {$file}");
            }
            $this->info("Updated central index: {$indexFile}");
            return Command::SUCCESS;
        }

        $this->info('Starting full sitemap generation...');
        $result = $sitemapService->generateAll();

        $this->info("Successfully generated {$result['total_sitemaps']} sub-sitemap(s) and central index:");
        foreach ($result['files'] as $file) {
            $this->line(" - {$file}");
        }

        return Command::SUCCESS;
    }
}

