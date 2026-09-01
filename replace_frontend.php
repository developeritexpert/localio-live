<?php
$filePath = '/home/ubuntu/projects/localio-live/resources/views/livewire/category-page.blade.php';
$content = file_get_contents($filePath);
$pattern = '/@if\(!empty\(\$category->translation->top_rated_listing_subtext\)\)\s*<p style="font-size: 14px; color: #555; margin-bottom: 0;">\s*\{\{ \$category->translation->top_rated_listing_subtext \}\}\s*<\/p>\s*@endif/s';
$replace = '@if(!empty($category->translation->top_rated_listing_subtext))
                                <div style="font-size: 14px; color: #555; margin-bottom: 0;" class="top-rated-subtext-container ck-content">
                                    {!! $category->translation->top_rated_listing_subtext !!}
                                </div>
                            @endif';
$content = preg_replace($pattern, $replace, $content);
file_put_contents($filePath, $content);
echo "Updated category-page.blade.php.\n";
