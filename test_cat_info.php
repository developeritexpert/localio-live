<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cats = \App\Models\Category::with('translations')->get();
foreach($cats as $c) {
    $t = $c->translations->first();
    $slug = $t->slug ?? 'no-slug';
    if (in_array($slug, ['web-hosting', 'website-builders'])) {
        echo "=== CAT: " . $slug . " (ID: " . $c->id . ", parent_id: " . ($c->parent_id ?? 'null') . ") ===\n";
        $topAff = \App\Models\Business::where('category_id', $c->id)
            ->where('status', 1)
            ->where('is_affiliate', 1)
            ->get();
        echo "topAffiliatedBusinesses count: " . $topAff->count() . "\n";
        foreach($topAff as $b) {
            $bt = $b->translations->first();
            echo "   Affiliate Biz: " . ($bt->name ?? 'no-name') . " (ID: " . $b->id . ")\n";
        }
        
        $allBiz = \App\Models\Business::where('category_id', $c->id)->get();
        echo "All biz count directly in category_id: " . $allBiz->count() . "\n";
        foreach($allBiz as $b) {
            $bt = $b->translations->first();
            echo "   Biz: " . ($bt->name ?? 'no-name') . " (ID: " . $b->id . ", is_affiliate=" . $b->is_affiliate . ", status=" . $b->status . ")\n";
        }
    }
}
