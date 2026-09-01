<?php
$filePath = '/home/ubuntu/projects/localio-live/app/Http/Controllers/Admin/CategoriesController.php';
$content = file_get_contents($filePath);

$search = "'top_rated_listing_subtext'  => \$request->input('top_rated_listing_subtext'),";
$replace = "'top_rated_listing_subtext'  => \$request->input('top_rated_listing_subtext'),
                    'compare_alternatives_subtext' => \$request->input('compare_alternatives_subtext'),
                    'community_says_subtext' => \$request->input('community_says_subtext'),";

$content = str_replace($search, $replace, $content);
file_put_contents($filePath, $content);
echo "Updated CategoriesController save logic.\n";
