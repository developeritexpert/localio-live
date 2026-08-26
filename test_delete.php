
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BaseLanguage;
use App\Models\Language;

// 1. Create a dummy base language
$baseLang = BaseLanguage::create([
    'name' => 'Dummy Test Lang',
    'bcp47_language_id' => 1,
    'language_tag' => 'Dummy',
    'status' => 1,
    'is_master' => false,
]);
echo "Created dummy base lang ID: {$baseLang->id}
";

// 2. Try to delete it via controller simulation
$controller = new \App\Http\Controllers\Admin\BaseLanguageController();
$response = $controller->delete($baseLang->id);
echo "Attempt 1 (no associated languages) response status: " . get_class($response) . "
";
echo "Flash error: " . session('error') . "
";
echo "Flash success: " . session('success') . "
";

// Recreate it since it succeeded
$baseLang = BaseLanguage::create([
    'name' => 'Dummy Test Lang 2',
    'bcp47_language_id' => 1,
    'language_tag' => 'Dummy2',
    'status' => 1,
    'is_master' => false,
]);

// Attach a fake language
$lang = Language::create([
    'name' => 'Fake Region',
    'base_language_id' => $baseLang->id,
    'status' => 1,
]);
echo "Attached fake region ID: {$lang->id}
";

// Try delete again
$response2 = $controller->delete($baseLang->id);
echo "Attempt 2 (with associated language) response status: " . get_class($response2) . "
";
echo "Flash error: " . session('error') . "
";
echo "Flash success: " . session('success') . "
";

// Clean up
$lang->delete();
$baseLang->delete();
echo "Cleanup done.
";
