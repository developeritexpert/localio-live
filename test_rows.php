<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \DB::table('category_translations')->whereIn('category_id', [11, 20, 30])->get();
foreach($rows as $r) {
    echo "cat_id={$r->category_id}, lang_id={$r->lang_id}, name={$r->name}, slug={$r->slug}\n";
}
