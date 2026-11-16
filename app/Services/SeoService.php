<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Support\Facades\Request;

class SeoService
{
    /**
     * Get the base URL (forced HTTPS in production or for localio.com).
     */
    public static function getBaseUrl(): string
    {
        $currentUrl = Request::url();
        if (str_contains($currentUrl, 'localio.com')) {
            return 'https://localio.com';
        }

        $appUrl = config('app.url', 'https://localio.com');
        $baseUrl = rtrim((string) $appUrl, '/');

        if (!preg_match('#^https?://#i', $baseUrl)) {
            $baseUrl = 'https://' . $baseUrl;
        }

        return $baseUrl;
    }

    /**
     * Get canonical URL for current request.
     */
    public static function getCanonicalUrl(?string $override = null): string
    {
        if (!empty($override)) {
            return $override;
        }

        $url = Request::url();
        if (str_contains($url, 'localio.com')) {
            $url = preg_replace('#^http://#i', 'https://', $url);
        }

        return $url;
    }

    /**
     * Global Organization & WebSite JSON-LD Schema.
     */
    public static function getWebSiteAndOrgSchema(): string
    {
        $baseUrl = self::getBaseUrl();
        $currentLocale = getCurrentLocale() ?: 'en-us';

        $data = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id'   => $baseUrl . '/#organization',
                    'name'  => 'Localio',
                    'url'   => $baseUrl,
                    'logo'  => [
                        '@type'      => 'ImageObject',
                        '@id'        => $baseUrl . '/#logo',
                        'url'        => $baseUrl . '/front/img/icon.svg',
                        'caption'    => 'Localio',
                    ],
                ],
                [
                    '@type'       => 'WebSite',
                    '@id'         => $baseUrl . '/#website',
                    'url'         => $baseUrl,
                    'name'        => 'Localio',
                    'description' => 'Compare the best digital products, web hosting, and software tools with real user reviews.',
                    'publisher'   => [
                        '@id' => $baseUrl . '/#organization',
                    ],
                    'potentialAction' => [
                        '@type'       => 'SearchAction',
                        'target'      => [
                            '@type'       => 'EntryPoint',
                            'urlTemplate' => $baseUrl . '/' . $currentLocale . '/search?search={search_term_string}',
                        ],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ];

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * BreadcrumbList JSON-LD Schema.
     *
     * @param array $items Array of ['name' => '...', 'url' => '...']
     */
    public static function getBreadcrumbsSchema(array $items): string
    {
        $elements = [];
        $position = 1;

        foreach ($items as $item) {
            if (empty($item['name'])) {
                continue;
            }
            $entry = [
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $item['name'],
            ];
            if (!empty($item['url'])) {
                $entry['item'] = $item['url'];
            }
            $elements[] = $entry;
        }

        $data = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Business Detail Page JSON-LD Schema (Product / SoftwareApplication + AggregateRating).
     */
    public static function getBusinessSchema($business, $averageRating = null, int $ratingCount = 0, ?string $description = null): string
    {
        if (!$business) {
            return '';
        }

        $baseUrl = self::getBaseUrl();
        $name = optional($business->translations->first())->name ?? 'Business';
        $desc = $description ?? optional($business->translations->first())->short_description ?? "{$name} reviews and ratings on Localio.";
        $url = self::getCanonicalUrl();

        $logo = $business->hasLogo() ? asset($business->getRawOriginal('icon_id') ?? $business->icon_id) : $baseUrl . '/front/img/icon.svg';

        $data = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $name,
            'description' => strip_tags($desc),
            'url'         => $url,
            'image'       => $logo,
            'brand'       => [
                '@type' => 'Brand',
                'name'  => $name,
            ],
        ];

        // Only include AggregateRating if ratings exist
        if ($averageRating !== null && (float) $averageRating > 0 && $ratingCount > 0) {
            $data['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => number_format((float) $averageRating, 1, '.', ''),
                'bestRating'  => '5',
                'worstRating' => '1',
                'ratingCount' => (int) $ratingCount,
            ];
        }

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Category Page JSON-LD Schema (CollectionPage).
     */
    public static function getCategorySchema($category, ?string $metaTitle = null, ?string $metaDescription = null): string
    {
        if (!$category) {
            return '';
        }

        $title = $metaTitle ?? optional($category->translations->first())->name ?? 'Category';
        $desc  = $metaDescription ?? optional($category->translations->first())->description ?? '';
        $url   = self::getCanonicalUrl();

        $data = [
            '@context'    => 'https://schema.org',
            '@type'       => 'CollectionPage',
            'name'        => $title,
            'description' => strip_tags($desc),
            'url'         => $url,
        ];

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Generate multi-language hreflang alternate tags.
     *
     * @param Business|null $business
     * @return array Array of ['hreflang' => '...', 'href' => '...']
     */
    public static function getHreflangLinks($business = null): array
    {
        $pathSegments = Request::segments();
        if (empty($pathSegments)) {
            return [];
        }

        $baseUrl = self::getBaseUrl();
        $activeLangs = Language::where('status', 1)->get();

        // If on a business profile, filter languages to where the business is available
        if ($business instanceof Business && (int) $business->active_all_countries !== 1) {
            $allowedCountryIds = $business->countries()->pluck('countries.id')->toArray();
            $activeLangs = $activeLangs->filter(function ($lang) use ($allowedCountryIds) {
                return in_array($lang->country_id, $allowedCountryIds);
            });
        }

        if ($activeLangs->isEmpty()) {
            return [];
        }

        $links = [];
        $hasEnUs = false;

        foreach ($activeLangs as $lang) {
            $segments = $pathSegments;
            $segments[0] = strtolower($lang->lang_code);
            $href = $baseUrl . '/' . implode('/', $segments);

            // BCP 47 code for hreflang (e.g. "en-us", "es-cl")
            $hreflangCode = !empty($lang->html_lang) ? strtolower($lang->html_lang) : strtolower($lang->lang_code);

            $links[] = [
                'hreflang' => $hreflangCode,
                'href'     => $href,
            ];

            if ($hreflangCode === 'en-us') {
                $hasEnUs = true;
            }
        }

        // x-default fallback: points to en-us version or first available locale
        $defaultSegments = $pathSegments;
        $defaultSegments[0] = $hasEnUs ? 'en-us' : strtolower($activeLangs->first()->lang_code);
        $xDefaultUrl = $baseUrl . '/' . implode('/', $defaultSegments);

        $links[] = [
            'hreflang' => 'x-default',
            'href'     => $xDefaultUrl,
        ];

        return $links;
    }
}
