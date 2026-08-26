<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$langs = DB::table('languages')->where('lang_code', 'like', '%fr-ca%')->get();
foreach($langs as $l) {
    echo "ID: " . $l->id . " - Name: " . $l->name . " - Code: " . $l->lang_code . " - Country ID: " . $l->country_id . " - Status: " . $l->status . "\n";
}
