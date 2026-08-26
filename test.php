
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$record = App\Models\TopProductContent::where('meta_key', 'top_rated_text_sections')->first();
if ($record) {
    echo "Found record for lang_id: " . $record->lang_id . "
";
    echo "Value: " . $record->meta_value . "
";
} else {
    echo "No record found.
";
}
