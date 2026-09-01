<?php
$filePath = '/home/ubuntu/projects/localio-live/app/Http/Controllers/Admin/CategoriesController.php';
$content = file_get_contents($filePath);

$duplicate = "        if (\$request->hasFile('faq_image')) {
            \$mediaFaq = \$this->mediaservice->uploadMedia(\$request->file('faq_image'), 'category/faq');
            \$category->faq_image = \$mediaFaq->id;
        }
        if (\$request->hasFile('faq_image')) {
            \$mediaFaq = \$this->mediaservice->uploadMedia(\$request->file('faq_image'), 'category/faq');
            \$category->faq_image = \$mediaFaq->id;
        }";

$single = "        if (\$request->hasFile('faq_image')) {
            \$mediaFaq = \$this->mediaservice->uploadMedia(\$request->file('faq_image'), 'category/faq');
            \$category->faq_image = \$mediaFaq->id;
        }";

$content = str_replace($duplicate, $single, $content);
$duplicate_n = str_replace("\r\n", "\n", $duplicate);
$single_n = str_replace("\r\n", "\n", $single);
$content = str_replace($duplicate_n, $single_n, $content);

file_put_contents($filePath, $content);
echo "Cleaned CategoriesController.\n";
