<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach([11, 30] as $id) {
    echo "=== CAT $id ===\n";
    $trans = \DB::table('category_translations')->where('category_id', $id)->get();
    foreach($trans as $t) {
        echo "  Lang ID: {$t->lang_id}, Name: {$t->name}, Slug: {$t->slug}\n";
    }
}
