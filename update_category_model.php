<?php
$filePath = '/home/ubuntu/projects/localio-live/app/Models/Category.php';
$content = file_get_contents($filePath);

$search1 = "'top_rated_image',";
$replace1 = "'top_rated_image', 'faq_image',";
$content = str_replace($search1, $replace1, $content);

$search2 = "    public function topRatedImageMedia()
    {
        return \$this->hasOne(Media::class, 'id', 'top_rated_image');
    }";

$replace2 = "    public function topRatedImageMedia()
    {
        return \$this->hasOne(Media::class, 'id', 'top_rated_image');
    }

    public function faqImageMedia()
    {
        return \$this->hasOne(Media::class, 'id', 'faq_image');
    }";

$content = str_replace($search2, $replace2, $content);
$search2_n = str_replace("\r\n", "\n", $search2);
$replace2_n = str_replace("\r\n", "\n", $replace2);
$content = str_replace($search2_n, $replace2_n, $content);

file_put_contents($filePath, $content);
echo "Category model updated.\n";
