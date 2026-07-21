<?php
require '/home/ubuntu/projects/localio-live/vendor/autoload.php';
$app = require_once '/home/ubuntu/projects/localio-live/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$category = \App\Models\Category::find(10);
$language_id = 1;
$validate = [
    'name' => 'Food & Beverage',
    'title' => 'Title',
    'description' => 'Desc',
    'comparison_slug' => 'test-compare-slug-123'
];
$slug = 'food-beverage-10-2';
$res = \App\Models\CategoryTranslation::updateOrCreate(
    [
        'lang_id' => (int) $language_id,
        'category_id' => $category->id
    ],
    [
        'category_id'  => $category->id,
        'lang_id'      => $language_id,
        'name'         => $validate['name'],
        'title'        => $validate['title'] ?? null,
        'description'  => $validate['description'],
        'slug'         => $slug,
        'comparison_slug' => $validate['comparison_slug'] ?? null,
        'is_important' => 0,
    ]
);

print_r($res->toArray());
