<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$b = \App\Models\Business::with(['category', 'subCategories'])->find(34);
echo "Bluehost ID: " . $b->id . "\n";
echo "category_id: " . $b->category_id . "\n";
foreach($b->subCategories as $sc) {
    echo "subCategory: " . $sc->id . " parent_id: " . $sc->parent_id . "\n";
}
