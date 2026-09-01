<?php
$filePath = '/home/ubuntu/projects/localio-live/app/Http/Controllers/Admin/CategoriesController.php';
$content = file_get_contents($filePath);
$search = "        \$category->parent_id = \$request->parent_id ?? null;\n\n        if (\$request->hasFile('image')) {";
$replace = "        \$category->parent_id = \$request->parent_id ?? null;\n        \$category->page_title_addition = \$request->page_title_addition;\n        \$category->top_rated_listing_headline_addition = \$request->top_rated_listing_headline_addition;\n\n        if (\$request->hasFile('image')) {";
$content = str_replace($search, $replace, $content);

$search2 = "        \$category->parent_id = \$request->parent_id ?? null;\r\n\r\n        if (\$request->hasFile('image')) {";
$replace2 = "        \$category->parent_id = \$request->parent_id ?? null;\r\n        \$category->page_title_addition = \$request->page_title_addition;\r\n        \$category->top_rated_listing_headline_addition = \$request->top_rated_listing_headline_addition;\r\n\r\n        if (\$request->hasFile('image')) {";
$content = str_replace($search2, $replace2, $content);

file_put_contents($filePath, $content);
echo "Replaced.";
