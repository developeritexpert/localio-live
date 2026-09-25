<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bizs = \App\Models\Business::with(['category', 'subCategories'])->whereIn('id', [34, 35, 13, 17, 18])->get();
foreach($bizs as $b) {
    $name = $b->translations->first()->name ?? $b->id;
    echo "Biz: {$name} (ID: {$b->id})\n";
    echo "  category_id: {$b->category_id} (" . ($b->category?->translations->first()->name ?? 'none') . ", parent_id=" . ($b->category?->parent_id ?? 'none') . ")\n";
    echo "  is_affiliate: {$b->is_affiliate}\n";
    echo "  status: {$b->status}\n";
    $subCats = $b->subCategories->map(fn($sc) => $sc->id . ':' . ($sc->translations->first()->name ?? ''))->join(', ');
    echo "  subCategories pivot: [{$subCats}]\n";
}
