<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = ['/en-us/wizhosting', '/en-us'];

foreach ($urls as $url) {
    echo "=====================================\n";
    echo "Testing W3C for URL: $url\n";
    $request = Illuminate\Http\Request::create($url, 'GET');
    $response = $kernel->handle($request);
    $html = $response->getContent();

    $ch = curl_init("https://validator.w3.org/nu/?out=json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $html);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: text/html; charset=utf-8"]);
    $res = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($res, true);
    $messages = $data['messages'] ?? [];

    $errors = [];
    $warnings = [];
    foreach ($messages as $msg) {
        if (($msg['type'] ?? '') === 'error') {
            $errors[] = $msg;
        } else {
            $warnings[] = $msg;
        }
    }

    echo "Total Errors: " . count($errors) . "\n";
    echo "Total Warnings: " . count($warnings) . "\n";

    if (count($errors) > 0) {
        echo "Error breakdown:\n";
        $grouped = [];
        foreach ($errors as $e) {
            $m = $e['message'] ?? '';
            $key = strpos($m, 'wire:') !== false ? 'Livewire attribute (required)' : $m;
            $grouped[$key] = ($grouped[$key] ?? 0) + 1;
        }
        foreach ($grouped as $k => $c) {
            echo "  [$c x] $k\n";
        }
    }
    echo "\n";
}
