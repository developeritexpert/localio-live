<?php
$filePath = '/home/ubuntu/projects/localio-live/resources/views/Admin/categories/add.blade.php';
$content = file_get_contents($filePath);
$search = '<textarea class="form-control" id="top_rated_listing_subtext"';
$replace = '<textarea class="form-control rich-editor" id="top_rated_listing_subtext"';
$content = str_replace($search, $replace, $content);
file_put_contents($filePath, $content);
echo "Updated add.blade.php.\n";

$filePath2 = '/home/ubuntu/projects/localio-live/resources/views/livewire/category-page.blade.php';
$content2 = file_get_contents($filePath2);
$search2 = '@if(!empty($category->translation->top_rated_listing_subtext))
                                <p style="font-size: 14px; color: #555; margin-bottom: 0;">
                                    {{ $category->translation->top_rated_listing_subtext }}
                                </p>
                            @endif';
$replace2 = '@if(!empty($category->translation->top_rated_listing_subtext))
                                <div style="font-size: 14px; color: #555; margin-bottom: 0;" class="top-rated-subtext-container ck-content">
                                    {!! $category->translation->top_rated_listing_subtext !!}
                                </div>
                            @endif';

// It is possible line endings differ. Let's do regex instead for frontend.
