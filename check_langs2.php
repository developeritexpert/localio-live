<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$langs = DB::table('languages')->whereIn('country_id', [13, 34, 12, 2])->get();
foreach($langs as $l) {
    echo "ID: " . $l->id . " - Name: " . $l->name . " - Code: " . $l->lang_code . " - Country ID: " . $l->country_id . "\n";
}
