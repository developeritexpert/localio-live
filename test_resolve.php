<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slug = "web-hosting";
$locale = "en-us";
$matchedLang = \App\Models\Language::where('lang_code', $locale)->first();
$lang_id = $matchedLang ? $matchedLang->id : 1;

$category = \App\Models\Category::whereHas('translations', function ($query) use ($slug, $lang_id) {
    $query->where('slug', $slug)->where('lang_id', $lang_id);
})->orWhereHas('translations', function ($query) use ($slug) {
    $query->where('slug', $slug);
})->with(['translations' => fn($q) => $q->where('lang_id', $lang_id)])->first();

echo "Resolved Category ID: " . $category->id . "\n";
echo "Resolved Category Parent ID: " . var_export($category->parent_id, true) . "\n";
echo "Resolved Category Name: " . ($category->translations->first()->name ?? $category->name) . "\n";

// Now check what CategoryPage Livewire mount does:
$component = new \App\Livewire\CategoryPage();
$component->slug = $slug;
$component->mount($slug);
echo "CategoryPage Mounted Category ID: " . $component->category->id . "\n";

$renderedView = $component->render();
$data = $renderedView->getData();
echo "topAffiliatedBusinesses count in CategoryPage: " . (isset($data['topAffiliatedBusinesses']) ? $data['topAffiliatedBusinesses']->count() : 'none') . "\n";
foreach($data['topAffiliatedBusinesses'] as $b) {
    echo "  -> " . ($b->translations->first()->name ?? $b->id) . "\n";
}
echo "products count: " . $component->products->total() . "\n";
foreach($component->products as $p) {
    echo "  Product: " . ($p->translations->first()->name ?? $p->id) . "\n";
}
