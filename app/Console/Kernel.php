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
