<?php
$filePath = '/home/ubuntu/projects/localio-live/app/Http/Controllers/Admin/CategoriesController.php';
$content = file_get_contents($filePath);

// 1. Add faq_image upload handling
$search1 = "        if (\$request->hasFile('top_rated_image')) {
            \$mediaTopRated = \$this->mediaservice->uploadMedia(\$request->file('top_rated_image'), 'category/top-rated');
            \$category->top_rated_image = \$mediaTopRated->id;
        }";

$replace1 = "        if (\$request->hasFile('top_rated_image')) {
            \$mediaTopRated = \$this->mediaservice->uploadMedia(\$request->file('top_rated_image'), 'category/top-rated');
            \$category->top_rated_image = \$mediaTopRated->id;
        }
        if (\$request->hasFile('faq_image')) {
            \$mediaFaq = \$this->mediaservice->uploadMedia(\$request->file('faq_image'), 'category/faq');
            \$category->faq_image = \$mediaFaq->id;
        }";

$content = str_replace($search1, $replace1, $content);
$search1_n = str_replace("\r\n", "\n", $search1);
$replace1_n = str_replace("\r\n", "\n", $replace1);
$content = str_replace($search1_n, $replace1_n, $content);

// 2. Add faq fields to translation array
$search2 = "                    'community_says_subtext' => \$request->input('community_says_subtext'),";
$replace2 = "                    'community_says_subtext' => \$request->input('community_says_subtext'),
                    'faq_h1_title' => \$request->input('faq_h1_title'),
                    'faq_subtext' => \$request->input('faq_subtext'),
                    'faq_about_headline' => \$request->input('faq_about_headline'),";

$content = str_replace($search2, $replace2, $content);
$search2_n = str_replace("\r\n", "\n", $search2);
$replace2_n = str_replace("\r\n", "\n", $replace2);
$content = str_replace($search2_n, $replace2_n, $content);

file_put_contents($filePath, $content);
echo "CategoriesController updated successfully.\n";
