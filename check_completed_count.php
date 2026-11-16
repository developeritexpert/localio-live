<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AiBatchJob;

$jobs = AiBatchJob::whereIn('id', [49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59])->get();
foreach ($jobs as $j) {
    echo "Batch {$j->id}: provider={$j->provider}, status={$j->status}, total={$j->total_requests}, comp={$j->completed_requests}, failed={$j->failed_requests}" . PHP_EOL;
}
