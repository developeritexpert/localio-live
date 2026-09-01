<?php
$filePath = '/home/ubuntu/projects/localio-live/app/Models/CategoryTranslation.php';
$content = file_get_contents($filePath);
$search = "'top_rated_listing_subtext'";
$replace = "'top_rated_listing_subtext',\n        'compare_alternatives_subtext',\n        'community_says_subtext'";
$content = str_replace($search, $replace, $content);
file_put_contents($filePath, $content);
echo "Updated CategoryTranslation model.\n";
