<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$env = app('view');
echo "Testing sections stack:\n";
$env->startSection('meta_title', 'My Title');
echo "After startSection(meta_title, val):\n";
echo "Section Stack: " . json_encode($env->getSections()) . "\n";
echo "Section Keys: " . json_encode(array_keys($env->getSections())) . "\n";

$env->startSection('content');
echo "Inside content section...\n";
echo "ob_get_level: " . ob_get_level() . "\n";
echo "Buffered: Hello World\n";
$env->stopSection();

echo "After stopSection:\n";
echo "Section Keys: " . json_encode(array_keys($env->getSections())) . "\n";
