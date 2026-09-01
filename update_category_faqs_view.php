<?php
$filePath = '/home/ubuntu/projects/localio-live/resources/views/User/category/category_faqs.blade.php';
$content = file_get_contents($filePath);

// 1. Update Header Row (H1 Title, Subtext with CKEditor content support, and Top Right SVG Graphic)
$searchHeader = <<<HTML
        <!-- Category Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700; margin: 0; line-height: 1.2; color: #1e3050;">
                                {{ \$catName }} FAQs
                            </h1>
                        </div>
                        <p style="font-size: 16px; color: #444; margin-top: 4px; margin-bottom: 0; font-weight:400;">
                            Frequently asked questions and answers about {{ strtolower(\$catName) }} on Localio.
                        </p>
                    </div>
                </div>
            </div>
        </div>
HTML;

$replaceHeader = <<<HTML
        <!-- Category Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700; margin: 0; line-height: 1.2; color: #1e3050;">
                                {{ !empty(\$categoryTranslation->faq_h1_title) ? \$categoryTranslation->faq_h1_title : (\$catName . ' FAQs') }}
                            </h1>
                        </div>
                        <div class="faq-subtext-container ck-content" style="font-size: 16px; color: #444; margin-top: 4px; margin-bottom: 0; font-weight: 400;">
                            @if(!empty(\$categoryTranslation->faq_subtext))
                                {!! \$categoryTranslation->faq_subtext !!}
                            @else
                                <p style="margin: 0;">
                                    Frequently asked questions and answers about {{ strtolower(\$catName) }} on Localio.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-md-end mt-3 mt-md-0">
                @if(isset(\$category) && \$category->faq_image && \$category->faqImageMedia)
                    <img src="{{ asset(\$category->faqImageMedia->dir_path . '/' . \$category->faqImageMedia->file_name) }}" alt="{{ \$catName ?? '' }} FAQs" style="max-width: 300px; max-height: 100px; object-fit: contain;">
                @endif
            </div>
        </div>
HTML;

$content = str_replace($searchHeader, $replaceHeader, $content);
$searchHeader_n = str_replace("\r\n", "\n", $searchHeader);
$replaceHeader_n = str_replace("\r\n", "\n", $replaceHeader);
$content = str_replace($searchHeader_n, $replaceHeader_n, $content);

// 2. Update "About [Category]" Section Headline
$searchAbout = <<<HTML
                <!-- Section 1: Intro Title & Description -->
                <div class="faq-top-intro mb-4">
                    <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                        About {{ \$catName }}
                    </h2>
HTML;

$replaceAbout = <<<HTML
                <!-- Section 1: Intro Title & Description -->
                <div class="faq-top-intro mb-4">
                    <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                        {{ !empty(\$categoryTranslation->faq_about_headline) ? \$categoryTranslation->faq_about_headline : ('About ' . \$catName) }}
                    </h2>
HTML;

$content = str_replace($searchAbout, $replaceAbout, $content);
$searchAbout_n = str_replace("\r\n", "\n", $searchAbout);
$replaceAbout_n = str_replace("\r\n", "\n", $replaceAbout);
$content = str_replace($searchAbout_n, $replaceAbout_n, $content);

// 3. Add CSS for faq-subtext-container paragraphs
$cssSearch = "        .top-rated-heading-sec {";
$cssReplace = "        .faq-subtext-container p { margin-bottom: 0 !important; }\n        .faq-subtext-container p:not(:last-child) { margin-bottom: 8px !important; }\n        .top-rated-heading-sec {";
$content = str_replace($cssSearch, $cssReplace, $content);

file_put_contents($filePath, $content);
echo "category_faqs.blade.php updated successfully.\n";
