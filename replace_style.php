<?php
$filePath = '/home/ubuntu/projects/localio-live/resources/views/livewire/category-page.blade.php';
$content = file_get_contents($filePath);
$pattern = '/<div style="font-size: 14px; color: #555; margin-bottom: 0;" class="top-rated-subtext-container ck-content">/s';
$replace = '<style>
                                    .top-rated-subtext-container p { margin-bottom: 0 !important; }
                                    .top-rated-subtext-container p:not(:last-child) { margin-bottom: 10px !important; }
                                </style>
                                <div style="font-size: 14px; color: #555; margin-bottom: 0;" class="top-rated-subtext-container ck-content">';
$content = preg_replace($pattern, $replace, $content);
file_put_contents($filePath, $content);
echo "Updated category-page.blade.php style.\n";
