<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HomeContent;
use App\Models\Language;

$languages = Language::all();
foreach ($languages as $lang) {
    HomeContent::updateOrCreate(
        ['lang_id' => $lang->id, 'meta_key' => 'review_success_headline'],
        ['meta_value' => 'Thank you for your review!', 'type' => 'text']
    );
    HomeContent::updateOrCreate(
        ['lang_id' => $lang->id, 'meta_key' => 'review_success_text'],
        ['meta_value' => 'Your review has been successfully submitted and will help others make better decisions.', 'type' => 'text']
    );
}
echo "Done";
