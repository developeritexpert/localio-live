<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\ExpertGuideArticle;
use App\Models\ExpertGuideCategory;
use App\Models\Language;
use App\Models\Review;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SitemapService
{
    /**
     * Google and standard sitemap limits.
     * Manual specifies: split each group into files of up to 10,000 URL entries.
     */
    public const CHUNK_LIMIT = 10000;

    /**
     * Minimum reviews required for a business to be indexable on Localio.
     * Pages with < 5 approved reviews output <meta name="robots" content="noindex, follow">.
     */
    public const MIN_REVIEWS_FOR_INDEX = 5;

    /**
     * Get the base URL for sitemaps.
     */
    public function getBaseUrl(): string
    {
        $appUrl = config('app.url', 'https://localio.com');
        $baseUrl = rtrim((string) $appUrl, '/');

        if (!preg_match('#^https?://#i', $baseUrl)) {
            $baseUrl = 'https://' . $baseUrl;
        }

        // Force HTTPS in production / canonical sitemap URLs
        return preg_replace('#^http://#i', 'https://', $baseUrl);
    }

    /**
     * Get all active languages / locales.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Language>
     */
    public function getActiveLanguages()
    {
        return Language::where('status', 1)->orderBy('id')->get();
    }

    /**
     * Resolve the English/primary category slug for sitemap naming.
     */
    public function getCategorySlug(Category $category): string
    {
        // 1. Try English (lang_id = 1)
        $slug = $category->categoryTranslations()->where('lang_id', 1)->value('slug');

        // 2. Try any translation slug
        if (!$slug) {
            $slug = $category->categoryTranslations()->whereNotNull('slug')->where('slug', '!=', '')->value('slug');
        }

        // 3. Fallback from name or ID
        if (!$slug) {
            $name = $category->categoryTranslations()->where('lang_id', 1)->value('name');
            $slug = $name ? Str::slug($name) : "category-{$category->id}";
        }

        return $slug ?: "category-{$category->id}";
    }

    /**
     * Determine if a business is published and indexable (no noindex).
     */
    public function isBusinessIndexable(Business $business): bool
    {
        // Must be active / published
        if ((int) $business->status !== 1) {
            return false;
        }

        // Review threshold: businesses with < 5 reviews output noindex
        $reviewCount = $business->reviews()
            ->whereIn('status', ['published', 'active'])
            ->count();

        return $reviewCount >= self::MIN_REVIEWS_FOR_INDEX;
    }

    /**
     * Get all published, indexable, canonical locale URLs for a business.
     *
     * @param Business $business
     * @param \Illuminate\Support\Collection|null $activeLanguages
     * @return array<int, array{loc: string, lastmod: ?string, business_id: int, locale_id: int}>
     */
    public function getBusinessUrls(Business $business, $activeLanguages = null): array
    {
        if (!$this->isBusinessIndexable($business)) {
            return [];
        }

        $activeLanguages = $activeLanguages ?? $this->getActiveLanguages();
        $baseUrl = $this->getBaseUrl();

        // Check country restrictions
        $eligibleLanguages = $activeLanguages;
        if ((int) $business->active_all_countries !== 1) {
            $allowedCountryIds = $business->countries()->pluck('countries.id')->toArray();
            $eligibleLanguages = $activeLanguages->filter(function ($lang) use ($allowedCountryIds) {
                return in_array($lang->country_id, $allowedCountryIds);
            });
        }

        if ($eligibleLanguages->isEmpty()) {
            return [];
        }

        // Load translations mapped by lang_id
        $translations = $business->translations()
            ->where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get()
            ->keyBy(function ($item) {
                return (string) $item->lang_id;
            });

        // Default English fallback slug (lang_id = 1)
        $defaultSlug = $translations->get('1')?->slug
            ?? $translations->first()?->slug
            ?? null;

        if (!$defaultSlug) {
            return [];
        }

        $lastmod = ($business->updated_at ?: $business->created_at)?->toIso8601String();

        $urls = [];
        foreach ($eligibleLanguages as $lang) {
            $trans = $translations->get((string) $lang->id);
            $slug = ($trans && !empty($trans->slug)) ? $trans->slug : $defaultSlug;

            if (empty($slug)) {
                continue;
            }

            $loc = "{$baseUrl}/{$lang->lang_code}/{$slug}";

            $urls[] = [
                'loc'         => $loc,
                'lastmod'     => $lastmod,
                'business_id' => (int) $business->id,
                'locale_id'   => (int) $lang->id,
            ];
        }

        return $urls;
    }

    /**
     * Generate business XML sitemaps for a specific primary category / subcategory ID.
     *
     * @param int $categoryId
     * @return array<int, string> List of generated relative filenames
     */
    public function generateCategoryBusinessSitemaps(int $categoryId): array
    {
        $category = Category::find($categoryId);
        if (!$category) {
            $this->deleteCategoryBusinessSitemaps($categoryId);
            return [];
        }

        $slug = $this->getCategorySlug($category);
        $activeLanguages = $this->getActiveLanguages();

        // 1. Query published businesses where category_id matches exactly (primary category or subcategory)
        $businesses = Business::where('category_id', $categoryId)
            ->where('status', 1)
            ->with(['countries', 'translations'])
            ->orderBy('id', 'asc')
            ->get();

        // 2. Collect indexable URLs for each business
        $allUrls = [];
        foreach ($businesses as $business) {
            $bizUrls = $this->getBusinessUrls($business, $activeLanguages);
            foreach ($bizUrls as $urlEntry) {
                $allUrls[] = $urlEntry;
            }
        }

        // 3. If no URLs exist, delete existing category sitemaps and return
        if (empty($allUrls)) {
            $this->deleteCategoryBusinessSitemaps($categoryId);
            return [];
        }

        // 4. Deterministic sort: business_id, locale_id
        usort($allUrls, function ($a, $b) {
            if ($a['business_id'] === $b['business_id']) {
                return $a['locale_id'] <=> $b['locale_id'];
            }
            return $a['business_id'] <=> $b['business_id'];
        });

        // Ensure uniqueness of loc URLs
        $uniqueUrls = [];
        $seen = [];
        foreach ($allUrls as $entry) {
            if (!isset($seen[$entry['loc']])) {
                $seen[$entry['loc']] = true;
                $uniqueUrls[] = [
                    'loc'     => $entry['loc'],
                    'lastmod' => $entry['lastmod'],
                ];
            }
        }

        // 5. Split into chunks of at most 10,000 URL entries
        $chunks = array_chunk($uniqueUrls, self::CHUNK_LIMIT);
        $generatedFiles = [];

        foreach ($chunks as $index => $chunkUrls) {
            $number = sprintf('%03d', $index + 1);
            $filename = "sitemap-business-{$categoryId}-{$slug}-{$number}.xml";
            $xml = $this->buildUrlsetXml($chunkUrls);
            $this->writeXmlFile($filename, $xml);
            $generatedFiles[] = $filename;
        }

        // 6. Delete any obsolete chunk files for this category
        $this->cleanObsoleteCategoryFiles($categoryId, $generatedFiles);

        return $generatedFiles;
    }

    /**
     * Delete any sitemap files belonging to a specific category.
     */
    public function deleteCategoryBusinessSitemaps(int $categoryId): void
    {
        $pattern = public_path("sitemap-business-{$categoryId}-*.xml");
        $files = glob($pattern) ?: [];
        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }

    /**
     * Clean obsolete chunk files for a given category.
     */
    protected function cleanObsoleteCategoryFiles(int $categoryId, array $activeFiles): void
    {
        $pattern = public_path("sitemap-business-{$categoryId}-*.xml");
        $files = glob($pattern) ?: [];
        $activeBasenames = array_map('basename', $activeFiles);

        foreach ($files as $file) {
            if (!in_array(basename($file), $activeBasenames, true)) {
                File::delete($file);
            }
        }
    }

    /**
     * Generate sitemap for categories and subcategories landing pages.
     *
     * @return array<int, string>
     */
    public function generateCategoriesSitemap(): array
    {
        $baseUrl = $this->getBaseUrl();
        $activeLanguages = $this->getActiveLanguages();

        // Only active categories, excluding 301 alias/redirect categories (where target_category_id is set)
        $categories = Category::where('status', 1)
            ->whereNull('target_category_id')
            ->with(['categoryTranslations' => function ($query) {
                $query->where('status', 1);
            }])
            ->orderBy('id', 'asc')
            ->get();

        $urls = [];

        // All Categories index page across all locales
        foreach ($activeLanguages as $lang) {
            $urls[] = [
                'loc'     => "{$baseUrl}/{$lang->lang_code}/categories",
                'lastmod' => null,
            ];
        }

        // Each category landing page across active locales
        foreach ($categories as $cat) {
            $lastmod = ($cat->updated_at ?: $cat->created_at)?->toIso8601String();
            $translations = $cat->categoryTranslations->keyBy(function ($item) {
                return (string) $item->lang_id;
            });

            $defaultSlug = $translations->get('1')?->slug
                ?? $translations->first()?->slug
                ?? null;

            if (!$defaultSlug) {
                continue;
            }

            foreach ($activeLanguages as $lang) {
                $trans = $translations->get((string) $lang->id);
                $slug = ($trans && !empty($trans->slug)) ? $trans->slug : $defaultSlug;

                if (empty($slug)) {
                    continue;
                }

                $urls[] = [
                    'loc'     => "{$baseUrl}/{$lang->lang_code}/{$slug}",
                    'lastmod' => $lastmod,
                ];
            }
        }

        // Deduplicate
        $uniqueUrls = [];
        $seen = [];
        foreach ($urls as $entry) {
            if (!isset($seen[$entry['loc']])) {
                $seen[$entry['loc']] = true;
                $uniqueUrls[] = $entry;
            }
        }

        // Split into 10,000-entry chunks
        $chunks = array_chunk($uniqueUrls, self::CHUNK_LIMIT);
        $generatedFiles = [];

        foreach ($chunks as $index => $chunkUrls) {
            $number = sprintf('%03d', $index + 1);
            $filename = "sitemap-categories-{$number}.xml";
            $xml = $this->buildUrlsetXml($chunkUrls);
            $this->writeXmlFile($filename, $xml);
            $generatedFiles[] = $filename;
        }

        // Clean obsolete categories files
        $pattern = public_path("sitemap-categories-*.xml");
        $files = glob($pattern) ?: [];
        $activeBasenames = array_map('basename', $generatedFiles);
        foreach ($files as $file) {
            if (!in_array(basename($file), $activeBasenames, true)) {
                File::delete($file);
            }
        }

        return $generatedFiles;
    }

    /**
     * Generate sitemap for static, legal, and public meta pages.
     *
     * @return array<int, string>
     */
    public function generatePagesSitemap(): array
    {
        $baseUrl = $this->getBaseUrl();
        $activeLanguages = $this->getActiveLanguages();

        $routes = [
            '', // Homepage
            'about-localio',
            'contact',
            'Terms-of-service',
            'privacy-policy',
            'cookie-policy',
            'community-guidelines',
            'affiliate-disclosure',
            'copyright-dmca-policy',
            'legal-notice',
            'help-center',
            'blog',
            'expert-guides',
            'Exclusive-Businesses-Deals',
            'vendor-how-it-works',
            'vendor-get-listed',
        ];

        $urls = [];
        foreach ($activeLanguages as $lang) {
            foreach ($routes as $route) {
                $loc = empty($route)
                    ? "{$baseUrl}/{$lang->lang_code}"
                    : "{$baseUrl}/{$lang->lang_code}/{$route}";

                $urls[] = [
                    'loc'     => $loc,
                    'lastmod' => null,
                ];
            }
        }

        // Deduplicate
        $uniqueUrls = [];
        $seen = [];
        foreach ($urls as $entry) {
            if (!isset($seen[$entry['loc']])) {
                $seen[$entry['loc']] = true;
                $uniqueUrls[] = $entry;
            }
        }

        $chunks = array_chunk($uniqueUrls, self::CHUNK_LIMIT);
        $generatedFiles = [];

        foreach ($chunks as $index => $chunkUrls) {
            $number = sprintf('%03d', $index + 1);
            $filename = "sitemap-pages-{$number}.xml";
            $xml = $this->buildUrlsetXml($chunkUrls);
            $this->writeXmlFile($filename, $xml);
            $generatedFiles[] = $filename;
        }

        // Clean obsolete pages files
        $pattern = public_path("sitemap-pages-*.xml");
        $files = glob($pattern) ?: [];
        $activeBasenames = array_map('basename', $generatedFiles);
        foreach ($files as $file) {
            if (!in_array(basename($file), $activeBasenames, true)) {
                File::delete($file);
            }
        }

        return $generatedFiles;
    }

    /**
     * Generate sitemap for Expert Guides if published articles exist.
     *
     * @return array<int, string>
     */
    public function generateGuidesSitemap(): array
    {
        $baseUrl = $this->getBaseUrl();
        $activeLanguages = $this->getActiveLanguages();

        $articles = ExpertGuideArticle::where('status', 1)
            ->with(['articleTranslations' => function ($q) {
                $q->where('status', 1);
            }, 'category.translations'])
            ->get();

        if ($articles->isEmpty()) {
            $this->cleanObsoleteFiles('sitemap-guides-*.xml', []);
            return [];
        }

        $urls = [];
        foreach ($articles as $art) {
            $artLastmod = ($art->updated_at ?: $art->created_at)?->toIso8601String();
            $artTrans = $art->articleTranslations->keyBy(fn($t) => (string) $t->lang_id);
            $defaultArtSlug = $artTrans->get('1')?->slug ?? $artTrans->first()?->slug;

            $catTrans = $art->category?->translations?->keyBy(fn($t) => (string) $t->lang_id);
            $defaultCatSlug = $catTrans?->get('1')?->slug ?? $catTrans?->first()?->slug;

            if (!$defaultArtSlug || !$defaultCatSlug) {
                continue;
            }

            foreach ($activeLanguages as $lang) {
                $aSlug = $artTrans->get((string) $lang->id)?->slug ?: $defaultArtSlug;
                $cSlug = $catTrans?->get((string) $lang->id)?->slug ?: $defaultCatSlug;

                if (empty($aSlug) || empty($cSlug)) {
                    continue;
                }

                $urls[] = [
                    'loc'     => "{$baseUrl}/{$lang->lang_code}/expert-guides/{$cSlug}/{$aSlug}",
                    'lastmod' => $artLastmod,
                ];
            }
        }

        if (empty($urls)) {
            $this->cleanObsoleteFiles('sitemap-guides-*.xml', []);
            return [];
        }

        $chunks = array_chunk($urls, self::CHUNK_LIMIT);
        $generatedFiles = [];

        foreach ($chunks as $index => $chunkUrls) {
            $number = sprintf('%03d', $index + 1);
            $filename = "sitemap-guides-{$number}.xml";
            $xml = $this->buildUrlsetXml($chunkUrls);
            $this->writeXmlFile($filename, $xml);
            $generatedFiles[] = $filename;
        }

        $this->cleanObsoleteFiles('sitemap-guides-*.xml', $generatedFiles);
        return $generatedFiles;
    }

    /**
     * Rebuild the main central sitemap index: /sitemap.xml.
     * Always references every active, non-empty XML sitemap file.
     *
     * @return string Central index filename ('sitemap.xml')
     */
    public function updateIndex(): string
    {
        $baseUrl = $this->getBaseUrl();

        // Scan all sub-sitemap files
        $patterns = [
            public_path('sitemap-business-*.xml'),
            public_path('sitemap-categories-*.xml'),
            public_path('sitemap-guides-*.xml'),
            public_path('sitemap-pages-*.xml'),
        ];

        $allFiles = [];
        foreach ($patterns as $pattern) {
            $matches = glob($pattern) ?: [];
            foreach ($matches as $filepath) {
                // Ignore sitemap.xml itself
                if (basename($filepath) === 'sitemap.xml') {
                    continue;
                }
                // Verify file exists and is not empty
                if (File::exists($filepath) && File::size($filepath) > 0) {
                    $allFiles[] = $filepath;
                }
            }
        }

        // Sort files deterministically:
        // 1. Business sitemaps by category ID then chunk
        // 2. Categories
        // 3. Guides
        // 4. Pages
        usort($allFiles, function ($a, $b) {
            $nameA = basename($a);
            $nameB = basename($b);

            $isBizA = str_starts_with($nameA, 'sitemap-business-');
            $isBizB = str_starts_with($nameB, 'sitemap-business-');

            if ($isBizA && $isBizB) {
                preg_match('/^sitemap-business-(\d+)-(.*?)-(\d+)\.xml$/', $nameA, $mA);
                preg_match('/^sitemap-business-(\d+)-(.*?)-(\d+)\.xml$/', $nameB, $mB);

                $catIdA = isset($mA[1]) ? (int) $mA[1] : 0;
                $catIdB = isset($mB[1]) ? (int) $mB[1] : 0;
                if ($catIdA !== $catIdB) {
                    return $catIdA <=> $catIdB;
                }
                $chunkA = isset($mA[3]) ? (int) $mA[3] : 0;
                $chunkB = isset($mB[3]) ? (int) $mB[3] : 0;
                return $chunkA <=> $chunkB;
            }

            if ($isBizA !== $isBizB) {
                return $isBizA ? -1 : 1;
            }

            return strcmp($nameA, $nameB);
        });

        $sitemaps = [];
        foreach ($allFiles as $filepath) {
            $filename = basename($filepath);
            $loc = "{$baseUrl}/{$filename}";
            $mtime = filemtime($filepath);
            $lastmod = $mtime ? date('c', $mtime) : null;

            $sitemaps[] = [
                'loc'     => $loc,
                'lastmod' => $lastmod,
            ];
        }

        $xml = $this->buildIndexXml($sitemaps);
        $this->writeXmlFile('sitemap.xml', $xml);

        return 'sitemap.xml';
    }

    /**
     * Generate the entire suite of XML sitemaps and update the central index.
     *
     * @return array{index: string, total_sitemaps: int, files: array<string>}
     */
    public function generateAll(): array
    {
        $allActiveFiles = [];

        // 1. Categories
        $catFiles = $this->generateCategoriesSitemap();
        $allActiveFiles = array_merge($allActiveFiles, $catFiles);

        // 2. Static / Meta pages
        $pageFiles = $this->generatePagesSitemap();
        $allActiveFiles = array_merge($allActiveFiles, $pageFiles);

        // 3. Expert Guides
        $guideFiles = $this->generateGuidesSitemap();
        $allActiveFiles = array_merge($allActiveFiles, $guideFiles);

        // 4. Business sitemaps grouped by primary category_id
        $categoryIds = Business::where('status', 1)
            ->whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id')
            ->toArray();

        foreach ($categoryIds as $catId) {
            $bizFiles = $this->generateCategoryBusinessSitemaps((int) $catId);
            $allActiveFiles = array_merge($allActiveFiles, $bizFiles);
        }

        // 5. Clean up any stale business sitemaps for categories with no businesses
        $allBusinessSitemapsOnDisk = glob(public_path('sitemap-business-*.xml')) ?: [];
        $activeBasenames = array_map('basename', $allActiveFiles);
        foreach ($allBusinessSitemapsOnDisk as $file) {
            if (!in_array(basename($file), $activeBasenames, true)) {
                File::delete($file);
            }
        }

        // 6. Update central sitemap.xml index
        $indexFile = $this->updateIndex();

        return [
            'index'          => $indexFile,
            'total_sitemaps' => count($allActiveFiles),
            'files'          => array_merge([$indexFile], $allActiveFiles),
        ];
    }

    /**
     * Clean obsolete files matching a glob pattern.
     */
    protected function cleanObsoleteFiles(string $patternName, array $activeFiles): void
    {
        $pattern = public_path($patternName);
        $files = glob($pattern) ?: [];
        $activeBasenames = array_map('basename', $activeFiles);

        foreach ($files as $file) {
            if (!in_array(basename($file), $activeBasenames, true)) {
                File::delete($file);
            }
        }
    }

    /**
     * Build standard <urlset> XML.
     *
     * @param array<int, array{loc: string, lastmod?: ?string}> $urls
     */
    protected function buildUrlsetXml(array $urls): string
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        foreach ($urls as $entry) {
            $loc = htmlspecialchars($entry['loc'], ENT_QUOTES | ENT_XML1, 'UTF-8');
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            if (!empty($entry['lastmod'])) {
                $lastmod = htmlspecialchars($entry['lastmod'], ENT_QUOTES | ENT_XML1, 'UTF-8');
                $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>\n";
        return $xml;
    }

    /**
     * Build standard <sitemapindex> XML.
     *
     * @param array<int, array{loc: string, lastmod?: ?string}> $sitemaps
     */
    protected function buildIndexXml(array $sitemaps): string
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        foreach ($sitemaps as $entry) {
            $loc = htmlspecialchars($entry['loc'], ENT_QUOTES | ENT_XML1, 'UTF-8');
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            if (!empty($entry['lastmod'])) {
                $lastmod = htmlspecialchars($entry['lastmod'], ENT_QUOTES | ENT_XML1, 'UTF-8');
                $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            }
            $xml .= "  </sitemap>\n";
        }

        $xml .= "</sitemapindex>\n";
        return $xml;
    }

    /**
     * Write XML file atomically to public path.
     */
    protected function writeXmlFile(string $filename, string $content): void
    {
        $targetPath = public_path($filename);
        $tempPath = public_path('.tmp_' . uniqid(pathinfo($filename, PATHINFO_FILENAME), true) . '.xml');

        File::put($tempPath, $content);
        @chmod($tempPath, 0664);
        @rename($tempPath, $targetPath);
    }
}
