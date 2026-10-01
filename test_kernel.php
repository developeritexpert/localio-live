<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/en-us/crimson-leaf-cafe', 'GET');
$response = $kernel->handle($request);

$content = $response->getContent();
echo "RESPONSE LENGTH: " . strlen($content) . "\n";
$lines = explode("\n", $content);
echo "TOTAL LINES: " . count($lines) . "\n";
for ($i = 0; $i < min(10, count($lines)); $i++) {
    echo ($i + 1) . ": " . substr($lines[$i], 0, 80) . "\n";
}
for ($i = 0; $i < count($lines); $i++) {
    if (strpos(strtolower($lines[$i]), 'doctype') !== false) {
        echo "DOCTYPE at line " . ($i + 1) . ": " . substr($lines[$i], 0, 50) . "\n";
    }
}
