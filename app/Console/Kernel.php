<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\TranslateContent::class,
        \App\Console\Commands\CacheLanguages::class,
        \App\Console\Commands\FeatureTranslations::class,
        \App\Console\Commands\DispatchReviewModerationBatch::class,
        \App\Console\Commands\PollReviewModerationBatch::class,
        \App\Console\Commands\DetectModerationCases::class,
        \App\Console\Commands\DispatchReviewTranslationBatch::class,
        \App\Console\Commands\PollReviewTranslationBatch::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Poll AI batch results every 30 minutes
        $schedule->call(function () {
            $controller = app(\App\Http\Controllers\Admin\ContentGenerationController::class);
            $controller->pollBatches();
        })->everyThirtyMinutes()->name('poll-ai-batches')->withoutOverlapping();

        // -- 3-Layer Review Moderation Automation -----------------------------
        // Dispatch reviews awaiting Layer 3 GPT-5.6-Luna Batch (twice daily / every 12h)
        $schedule->command('moderation:dispatch-batch')
            ->twiceDaily(1, 13)
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/moderation_batch_dispatch.log'));

        // Poll OpenAI Batch status every 30 minutes and apply Layer 3 results
        $schedule->command('moderation:poll-batch')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/moderation_batch_poll.log'));

        // Scan for coordinated manipulation and IP clusters daily
        $schedule->command('moderation:detect-cases')
            ->dailyAt('02:30')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/moderation_cases.log'));

        // -- Automated Review Translations -----------------------------------
        // Dispatch pending review translations (daily at 03:00)
        $schedule->command('translations:dispatch-batch')
            ->dailyAt('03:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/translation_batch_dispatch.log'));

        // Poll translation batch completion every 30 minutes
        $schedule->command('translations:poll-batch')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/translation_batch_poll.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
