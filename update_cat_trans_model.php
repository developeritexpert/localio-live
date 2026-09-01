<?php
$filePath = '/home/ubuntu/projects/localio-live/app/Models/CategoryTranslation.php';
$content = file_get_contents($filePath);

$search = "'community_says_subtext'";
$replace = "'community_says_subtext',
        'faq_h1_title',
        'faq_subtext',
        'faq_about_headline'";

$content = str_replace($search, $replace, $content);
file_put_contents($filePath, $content);
echo "CategoryTranslation model updated.\n";
