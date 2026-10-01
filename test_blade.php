<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = Illuminate\Http\Request::create('/en-us/crimson-leaf-cafe', 'GET');
$app->instance('request', $req);

$controller = app(\App\Http\Controllers\User\ProductController::class);
$response = $controller->productDetail('en-us', 'crimson-leaf-cafe', $req);

if ($response instanceof \Illuminate\View\View) {
    $rendered = $response->render();
    echo "RENDERED LENGTH: " . strlen($rendered) . "\n";
    $lines = explode("\n", $rendered);
    echo "FIRST 5 LINES:\n";
    for ($i = 0; $i < min(5, count($lines)); $i++) {
        echo ($i + 1) . ": " . $lines[$i] . "\n";
    }
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos(strtolower($lines[$i]), 'doctype') !== false) {
            echo "DOCTYPE at line " . ($i + 1) . ": " . substr($lines[$i], 0, 50) . "\n";
        }
    }
} else {
    echo "Response is not a View! Class: " . get_class($response) . "\n";
}
