<?php
require '/home/ubuntu/projects/localio-live/vendor/autoload.php';
$app = require_once '/home/ubuntu/projects/localio-live/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

print_r(\Illuminate\Support\Facades\Schema::getColumnListing('category_translations'));
