<?php
$filePath = '/home/ubuntu/projects/localio-live/resources/views/livewire/category-page.blade.php';
$content = file_get_contents($filePath);

// Update Compare Alternatives section
$search1 = <<<HTML
                        <h2 style="font-size: 22px; font-weight: 700; color: #002347; margin-bottom: 16px;">
                            Compare {{ \$category->translations->name ?? \$category->name }} alternatives
                        </h2>
HTML;

$replace1 = <<<HTML
                        <h2 style="font-size: 22px; font-weight: 700; color: #002347; margin-bottom: 4px;">
                            Compare {{ \$category->translations->name ?? \$category->name }} alternatives
                        </h2>
                        @if(!empty(\$category->translations->compare_alternatives_subtext))
                            <style>
                                .compare-alternatives-subtext p { margin-bottom: 0 !important; }
                                .compare-alternatives-subtext p:not(:last-child) { margin-bottom: 10px !important; }
                            </style>
                            <div style="font-size: 14px; color: #64748b; margin-bottom: 16px;" class="compare-alternatives-subtext ck-content">
                                {!! \$category->translations->compare_alternatives_subtext !!}
                            </div>
                        @else
                            <div style="margin-bottom: 16px;"></div>
                        @endif
HTML;

$content = str_replace($search1, $replace1, $content);
$search1_2 = str_replace("\r\n", "\n", $search1);
$replace1_2 = str_replace("\r\n", "\n", $replace1);
$content = str_replace($search1_2, $replace1_2, $content);

// Update Community Says section
$search2 = <<<HTML
                            <div class="mb-3">
                                <h2 style="font-size: 22px; font-weight: 700; color: #002347; margin-bottom: 4px;">What the Localio community says</h2>
                                <p style="font-size: 14px; color: #64748b; margin: 0;">Most mentioned pros and cons shared by real users.</p>
                            </div>
HTML;

$replace2 = <<<HTML
                            <div class="mb-3">
                                <h2 style="font-size: 22px; font-weight: 700; color: #002347; margin-bottom: 4px;">What the Localio community says</h2>
                                @if(!empty(\$category->translations->community_says_subtext))
                                    <style>
                                        .community-says-subtext p { margin-bottom: 0 !important; }
                                        .community-says-subtext p:not(:last-child) { margin-bottom: 10px !important; }
                                    </style>
                                    <div style="font-size: 14px; color: #64748b; margin: 0;" class="community-says-subtext ck-content">
                                        {!! \$category->translations->community_says_subtext !!}
                                    </div>
                                @else
                                    <p style="font-size: 14px; color: #64748b; margin: 0;">Most mentioned pros and cons shared by real users.</p>
                                @endif
                            </div>
HTML;

$content = str_replace($search2, $replace2, $content);
$search2_2 = str_replace("\r\n", "\n", $search2);
$replace2_2 = str_replace("\r\n", "\n", $replace2);
$content = str_replace($search2_2, $replace2_2, $content);


file_put_contents($filePath, $content);
echo "Updated category-page.blade.php.\n";
