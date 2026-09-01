<?php
$filePath = '/home/ubuntu/projects/localio-live/app/Http/Controllers/Admin/CategoriesController.php';
$content = file_get_contents($filePath);

$dup = "                    'faq_h1_title' => \$request->input('faq_h1_title'),
                    'faq_subtext' => \$request->input('faq_subtext'),
                    'faq_about_headline' => \$request->input('faq_about_headline'),
                    'faq_h1_title' => \$request->input('faq_h1_title'),
                    'faq_subtext' => \$request->input('faq_subtext'),
                    'faq_about_headline' => \$request->input('faq_about_headline'),";

$single = "                    'faq_h1_title' => \$request->input('faq_h1_title'),
                    'faq_subtext' => \$request->input('faq_subtext'),
                    'faq_about_headline' => \$request->input('faq_about_headline'),";

$content = str_replace($dup, $single, $content);
$dup_n = str_replace("\r\n", "\n", $dup);
$single_n = str_replace("\r\n", "\n", $single);
$content = str_replace($dup_n, $single_n, $content);

file_put_contents($filePath, $content);
echo "Cleaned CategoriesController translation array.\n";
