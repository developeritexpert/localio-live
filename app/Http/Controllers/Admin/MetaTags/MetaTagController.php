<?php

namespace App\Http\Controllers\Admin\MetaTags;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\CategoryFeatureContent;
use App\Models\Feature;
use App\Models\FeatureTransalte;
use App\Models\Language;
use App\Models\MetaTagRule;
use App\Services\MetaTagService;
use Illuminate\Http\Request;

class MetaTagController extends Controller
{
    /**
     * Categories Meta Tags management page.
     */
    public function categories(Request $request)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));

        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $lang = $selectedLanguage ? $selectedLanguage->lang_code : 'en-us';
        session()->put('lang_id', $lang_id);
        session()->put('lang_code', $lang);

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        $rule = MetaTagRule::getRuleFor('categories', $lang_id);

        $parentCategories = Category::whereNull('parent_id')
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->get();

        foreach ($parentCategories as $parent) {
            $parent->metaData = MetaTagService::resolveCategoryMeta($parent, $lang_id, $rule);
            foreach ($parent->subCategories as $sub) {
                $sub->metaData = MetaTagService::resolveCategoryMeta($sub, $lang_id, $rule);
            }
        }

        return view('Admin.meta-tags.categories', compact(
            'parentCategories',
            'rule',
            'languages',
            'lang_id',
            'selectedLanguage'
        ));
    }

    /**
     * Businesses Meta Tags management page.
     */
    public function businesses(Request $request)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));

        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $lang = $selectedLanguage ? $selectedLanguage->lang_code : 'en-us';
        session()->put('lang_id', $lang_id);
        session()->put('lang_code', $lang);

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        $rule = MetaTagRule::getRuleFor('businesses', $lang_id);

        $businesses = Business::with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'category.translations' => fn($q) => $q->where('lang_id', $lang_id),
        ])->orderBy('id', 'asc')->get();

        foreach ($businesses as $business) {
            $business->metaData = MetaTagService::resolveBusinessMeta($business, $lang_id, $rule);
        }

        return view('Admin.meta-tags.businesses', compact(
            'businesses',
            'rule',
            'languages',
            'lang_id',
            'selectedLanguage'
        ));
    }

    /**
     * Categories FAQs Meta Tags management page.
     */
    public function categoriesFaqs(Request $request)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));

        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $lang = $selectedLanguage ? $selectedLanguage->lang_code : 'en-us';
        session()->put('lang_id', $lang_id);
        session()->put('lang_code', $lang);

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        $rule = MetaTagRule::getRuleFor('category_faqs', $lang_id);

        $parentCategories = Category::whereNull('parent_id')
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->get();

        foreach ($parentCategories as $parent) {
            $parent->metaData = MetaTagService::resolveCategoryFaqMeta($parent, $lang_id, $rule);
            foreach ($parent->subCategories as $sub) {
                $sub->metaData = MetaTagService::resolveCategoryFaqMeta($sub, $lang_id, $rule);
            }
        }

        return view('Admin.meta-tags.categories-faqs', compact(
            'parentCategories',
            'rule',
            'languages',
            'lang_id',
            'selectedLanguage'
        ));
    }

    /**
     * Features Meta Tags management page.
     */
    public function features(Request $request)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));

        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $lang = $selectedLanguage ? $selectedLanguage->lang_code : 'en-us';
        session()->put('lang_id', $lang_id);
        session()->put('lang_code', $lang);

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        $rule = MetaTagRule::getRuleFor('features', $lang_id);

        // Load all parent categories and their subcategories with translations
        $allCategories = Category::whereNull('parent_id')
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->orderBy('id', 'asc')
            ->get();

        foreach ($allCategories as $parent) {
            $pTrans = MetaTagService::getCategoryTranslation($parent, $lang_id);
            $parent->displayName = $pTrans?->name ?? $parent->name ?? ('Category #' . $parent->id);
            foreach ($parent->subCategories as $sub) {
                $sTrans = MetaTagService::getCategoryTranslation($sub, $lang_id);
                $sub->displayName = $sTrans?->name ?? $sub->name ?? ('Category #' . $sub->id);
            }
        }

        $query = Feature::with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'category.translations' => fn($q) => $q->where('lang_id', $lang_id),
        ]);

        if ($categoryId = $request->get('category_id')) {
            $selectedCat = Category::find($categoryId);
            if ($selectedCat && $selectedCat->parent_id === null) {
                // If a parent category is selected, include all its subcategories too
                $subIds = $selectedCat->subCategories()->pluck('id')->push((int)$categoryId)->toArray();
                $query->whereIn('category_id', $subIds);
            } else {
                $query->where('category_id', $categoryId);
            }
        }

        if ($search = $request->get('search')) {
            $query->whereHas('translations', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $features = $query->paginate(50)->withQueryString();

        foreach ($features as $feature) {
            $feature->metaData = MetaTagService::resolveFeatureMeta($feature, $feature->category, $lang_id, $rule);
        }

        return view('Admin.meta-tags.features', compact(
            'features',
            'allCategories',
            'rule',
            'languages',
            'lang_id',
            'selectedLanguage'
        ));
    }

    /**
     * Update default rules and character limits.
     */
    public function updateRules(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|string',
            'lang_id' => 'nullable|integer',
            'title_char_limit' => 'required|integer|min:10|max:255',
            'title_primary' => 'required|string|max:255',
            'title_secondary' => 'nullable|string|max:255',
            'title_tertiary' => 'nullable|string|max:255',
            'description_char_limit' => 'required|integer|min:20|max:500',
            'description_primary' => 'nullable|string',
            'description_secondary' => 'nullable|string',
            'description_tertiary' => 'nullable|string',
        ]);

        $entityType = $request->input('entity_type');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));

        $rule = MetaTagRule::where('entity_type', $entityType)
            ->where('lang_id', $langId)
            ->first();

        if (!$rule) {
            $rule = new MetaTagRule();
            $rule->entity_type = $entityType;
            $rule->lang_id = $langId;
        }

        $rule->title_char_limit = $request->input('title_char_limit');
        $rule->title_primary = $request->input('title_primary');
        $rule->title_secondary = $request->input('title_secondary');
        $rule->title_tertiary = $request->input('title_tertiary');
        $rule->title_quaternary = $request->input('title_quaternary');
        $rule->description_char_limit = $request->input('description_char_limit');
        $rule->description_primary = $request->input('description_primary');
        $rule->description_secondary = $request->input('description_secondary');
        $rule->description_tertiary = $request->input('description_tertiary');
        $rule->description_quaternary = $request->input('description_quaternary');
        $rule->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Default meta tag rules updated successfully!',
                'rule' => $rule
            ]);
        }

        $routeMap = [
            'categories' => 'admin.meta-tags.categories',
            'businesses' => 'admin.meta-tags.businesses',
            'business_profile' => 'admin.meta-tags.business-profile',
            'business_alternatives' => 'admin.meta-tags.business-alternatives',
            'business_faqs' => 'admin.meta-tags.business-faqs',
            'business_compare' => 'admin.meta-tags.business-compare',
            'business_reviews' => 'admin.meta-tags.business-reviews',
            'business_discussions' => 'admin.meta-tags.business-discussions',
            'category_faqs' => 'admin.meta-tags.categories-faqs',
            'category_discussions' => 'admin.meta-tags.category-discussions',
            'features' => 'admin.meta-tags.features',
        ];

        $redirectRoute = $routeMap[$entityType] ?? 'admin.meta-tags.categories';

        return redirect()->route($redirectRoute, ['lang_id' => $langId])
            ->with('success', 'Default meta tag rules updated successfully!');
    }

    /**
     * Categories single AJAX update.
     */
    public function updateSingleCategoryMeta(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'lang_id' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $catId = (int) $request->input('category_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $metaTitle = $request->input('meta_title', $request->input('title'));
        $metaDesc = $request->input('meta_description', $request->input('description'));

        $this->saveCategoryTranslationMeta($catId, $langId, $metaTitle, $metaDesc);

        $category = Category::find($catId);
        $resolved = MetaTagService::resolveCategoryMeta($category, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Category meta tags updated successfully.',
            'resolved' => $resolved
        ]);
    }

    /**
     * Reset category meta tags to dynamic auto-defaults.
     */
    public function resetCategoryMeta(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'lang_id' => 'nullable|integer',
        ]);

        $catId = (int) $request->input('category_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));

        $catTrans = CategoryTranslation::where('category_id', $catId)
            ->where('lang_id', $langId)
            ->first();

        if ($catTrans) {
            $catTrans->meta_title = null;
            $catTrans->meta_description = null;
            $catTrans->save();
        }

        $category = Category::find($catId);
        $resolved = MetaTagService::resolveCategoryMeta($category, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Category reset to auto dynamic default successfully.',
            'resolved' => $resolved
        ]);
    }

    /**
     * Bulk save all categories manual meta overrides.
     */
    public function updateBulkCategoriesMeta(Request $request)
    {
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $titles = $request->input('meta_title', []);
        $descriptions = $request->input('meta_description', []);

        $allCatIds = array_unique(array_merge(array_keys($titles), array_keys($descriptions)));

        foreach ($allCatIds as $catId) {
            if (!is_numeric($catId)) continue;

            $metaTitle = $titles[$catId] ?? null;
            $metaDesc = $descriptions[$catId] ?? null;

            $this->saveCategoryTranslationMeta((int) $catId, $langId, $metaTitle, $metaDesc);
        }

        return redirect()->route('admin.meta-tags.categories', ['lang_id' => $langId])
            ->with('success', 'All category meta tags updated successfully.');
    }

    /**
     * Categories FAQs single AJAX update.
     */
    public function updateSingleCategoryFaqMeta(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'lang_id' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $catId = (int) $request->input('category_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $metaTitle = $request->input('meta_title', $request->input('title'));
        $metaDesc = $request->input('meta_description', $request->input('description'));

        $this->saveCategoryTranslationFaqMeta($catId, $langId, $metaTitle, $metaDesc);

        $category = Category::find($catId);
        $resolved = MetaTagService::resolveCategoryFaqMeta($category, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Category FAQ meta tags updated successfully.',
            'resolved' => $resolved
        ]);
    }

    /**
     * Reset category FAQ meta tags to dynamic auto-defaults.
     */
    public function resetCategoryFaqMeta(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'lang_id' => 'nullable|integer',
        ]);

        $catId = (int) $request->input('category_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));

        $catTrans = CategoryTranslation::where('category_id', $catId)
            ->where('lang_id', $langId)
            ->first();

        if ($catTrans) {
            $catTrans->faq_meta_title = null;
            $catTrans->faq_meta_description = null;
            $catTrans->save();
        }

        $category = Category::find($catId);
        $resolved = MetaTagService::resolveCategoryFaqMeta($category, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Category FAQ reset to auto dynamic default successfully.',
            'resolved' => $resolved
        ]);
    }

    /**
     * Bulk save all categories FAQs manual meta overrides.
     */
    public function updateBulkCategoriesFaqsMeta(Request $request)
    {
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $titles = $request->input('meta_title', []);
        $descriptions = $request->input('meta_description', []);

        $allCatIds = array_unique(array_merge(array_keys($titles), array_keys($descriptions)));

        foreach ($allCatIds as $catId) {
            if (!is_numeric($catId)) continue;

            $metaTitle = $titles[$catId] ?? null;
            $metaDesc = $descriptions[$catId] ?? null;

            $this->saveCategoryTranslationFaqMeta((int) $catId, $langId, $metaTitle, $metaDesc);
        }

        return redirect()->route('admin.meta-tags.categories-faqs', ['lang_id' => $langId])
            ->with('success', 'All category FAQ meta tags updated successfully.');
    }

    /**
     * Businesses single AJAX update.
     */
    public function updateSingleBusinessMeta(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'lang_id' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $bizId = (int) $request->input('business_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $metaTitle = $request->input('meta_title', $request->input('title'));
        $metaDesc = $request->input('meta_description', $request->input('description'));

        $this->saveBusinessTranslationMeta($bizId, $langId, $metaTitle, $metaDesc);

        $business = Business::find($bizId);
        $resolved = MetaTagService::resolveBusinessMeta($business, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Business meta tags updated successfully.',
            'resolved' => $resolved
        ]);
    }

    /**
     * Reset business meta tags to dynamic auto-defaults.
     */
    public function resetBusinessMeta(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'lang_id' => 'nullable|integer',
        ]);

        $bizId = (int) $request->input('business_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));

        $bizTrans = BusinessTranslation::where('business_id', $bizId)
            ->where('lang_id', $langId)
            ->first();

        if ($bizTrans) {
            $bizTrans->meta_title = null;
            $bizTrans->meta_description = null;
            $bizTrans->save();
        }

        $business = Business::find($bizId);
        if ($business) {
            $business->meta_title = null;
            $business->meta_description = null;
            $business->save();
            $business->refresh();
        }

        $resolved = MetaTagService::resolveBusinessMeta($business, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Business reset to auto dynamic default successfully.',
            'resolved' => $resolved
        ]);
    }

    /**
     * Bulk save all businesses manual meta overrides.
     */
    public function updateBulkBusinessesMeta(Request $request)
    {
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $titles = $request->input('meta_title', []);
        $descriptions = $request->input('meta_description', []);

        $allBizIds = array_unique(array_merge(array_keys($titles), array_keys($descriptions)));

        foreach ($allBizIds as $bizId) {
            if (!is_numeric($bizId)) continue;

            $metaTitle = $titles[$bizId] ?? null;
            $metaDesc = $descriptions[$bizId] ?? null;

            $this->saveBusinessTranslationMeta((int) $bizId, $langId, $metaTitle, $metaDesc);
        }

        return redirect()->route('admin.meta-tags.businesses', ['lang_id' => $langId])
            ->with('success', 'All business meta tags updated successfully.');
    }

    /**
     * Features single AJAX update.
     */
    public function updateSingleFeatureMeta(Request $request)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
            'lang_id' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $featId = (int) $request->input('feature_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $metaTitle = $request->input('meta_title', $request->input('title'));
        $metaDesc = $request->input('meta_description', $request->input('description'));

        $this->saveFeatureTranslationMeta($featId, $langId, $metaTitle, $metaDesc);

        $feature = Feature::find($featId);
        $resolved = MetaTagService::resolveFeatureMeta($feature, $feature?->category, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Feature meta tags updated successfully.',
            'resolved' => $resolved
        ]);
    }

    /**
     * Reset feature meta tags to dynamic auto-defaults.
     */
    public function resetFeatureMeta(Request $request)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
            'lang_id' => 'nullable|integer',
        ]);

        $featId = (int) $request->input('feature_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));

        $featTrans = FeatureTransalte::where('feature_id', $featId)
            ->where('lang_id', $langId)
            ->first();

        if ($featTrans) {
            $featTrans->meta_title = null;
            $featTrans->meta_description = null;
            $featTrans->save();
        }

        $feature = Feature::find($featId);
        $resolved = MetaTagService::resolveFeatureMeta($feature, $feature?->category, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Feature reset to auto dynamic default successfully.',
            'resolved' => $resolved
        ]);
    }

    /**
     * Bulk save features manual meta overrides.
     */
    public function updateBulkFeaturesMeta(Request $request)
    {
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $titles = $request->input('meta_title', []);
        $descriptions = $request->input('meta_description', []);

        $allFeatIds = array_unique(array_merge(array_keys($titles), array_keys($descriptions)));

        foreach ($allFeatIds as $featId) {
            if (!is_numeric($featId)) continue;

            $metaTitle = $titles[$featId] ?? null;
            $metaDesc = $descriptions[$featId] ?? null;

            $this->saveFeatureTranslationMeta((int) $featId, $langId, $metaTitle, $metaDesc);
        }

        return redirect()->back()
            ->with('success', 'Feature meta tags updated successfully.');
    }

    /**
     * Helper to update or create CategoryTranslation.
     */
    protected function saveCategoryTranslationMeta(int $catId, int $langId, ?string $metaTitle, ?string $metaDesc)
    {
        $catTrans = CategoryTranslation::where('category_id', $catId)
            ->where('lang_id', $langId)
            ->first();

        if ($catTrans) {
            $catTrans->meta_title = $metaTitle;
            $catTrans->meta_description = $metaDesc;
            $catTrans->save();
            return $catTrans;
        }

        $engTrans = CategoryTranslation::where('category_id', $catId)->where('lang_id', 1)->first();
        $baseSlug = $engTrans?->slug ?: ('category-' . $catId);
        $targetSlug = $baseSlug . '-' . $langId;

        $counter = 1;
        while (CategoryTranslation::where('slug', $targetSlug)->exists()) {
            $targetSlug = $baseSlug . '-' . $langId . '-' . $counter++;
        }

        return CategoryTranslation::create([
            'category_id' => $catId,
            'lang_id' => $langId,
            'status' => 1,
            'name' => $engTrans?->name ?? ('Category #' . $catId),
            'slug' => $targetSlug,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
        ]);
    }

    /**
     * Helper to update or create CategoryTranslation FAQ meta fields.
     */
    protected function saveCategoryTranslationFaqMeta(int $catId, int $langId, ?string $metaTitle, ?string $metaDesc)
    {
        $catTrans = CategoryTranslation::where('category_id', $catId)
            ->where('lang_id', $langId)
            ->first();

        if ($catTrans) {
            $catTrans->faq_meta_title = $metaTitle;
            $catTrans->faq_meta_description = $metaDesc;
            $catTrans->save();
            return $catTrans;
        }

        $engTrans = CategoryTranslation::where('category_id', $catId)->where('lang_id', 1)->first();
        $baseSlug = $engTrans?->slug ?: ('category-' . $catId);
        $targetSlug = $baseSlug . '-' . $langId;

        $counter = 1;
        while (CategoryTranslation::where('slug', $targetSlug)->exists()) {
            $targetSlug = $baseSlug . '-' . $langId . '-' . $counter++;
        }

        return CategoryTranslation::create([
            'category_id' => $catId,
            'lang_id' => $langId,
            'status' => 1,
            'name' => $engTrans?->name ?? ('Category #' . $catId),
            'slug' => $targetSlug,
            'faq_meta_title' => $metaTitle,
            'faq_meta_description' => $metaDesc,
        ]);
    }

    /**
     * Helper to update or create BusinessTranslation meta fields.
     */
    protected function saveBusinessTranslationMeta(int $bizId, int $langId, ?string $metaTitle, ?string $metaDesc)
    {
        $metaTitle = $metaTitle !== null ? trim($metaTitle) : null;
        $metaDesc = $metaDesc !== null ? trim($metaDesc) : null;

        $metaTitleVal = ($metaTitle !== '' && $metaTitle !== null) ? $metaTitle : null;
        $metaDescVal = ($metaDesc !== '' && $metaDesc !== null) ? $metaDesc : null;

        $bizTrans = BusinessTranslation::where('business_id', $bizId)
            ->where('lang_id', $langId)
            ->first();

        if ($bizTrans) {
            $bizTrans->meta_title = $metaTitleVal;
            $bizTrans->meta_description = $metaDescVal;
            $bizTrans->save();
        } else {
            $engTrans = BusinessTranslation::where('business_id', $bizId)->where('lang_id', 1)->first();
            $baseSlug = $engTrans?->slug ?: ('business-' . $bizId);
            $targetSlug = $baseSlug . '-' . $langId;

            $counter = 1;
            while (BusinessTranslation::where('slug', $targetSlug)->exists()) {
                $targetSlug = $baseSlug . '-' . $langId . '-' . $counter++;
            }

            $bizTrans = BusinessTranslation::create([
                'business_id' => $bizId,
                'lang_id' => $langId,
                'status' => 1,
                'name' => $engTrans?->name ?? ('Business #' . $bizId),
                'slug' => $targetSlug,
                'meta_title' => $metaTitleVal,
                'meta_description' => $metaDescVal,
            ]);
        }

        // Also keep legacy businesses table synchronized
        $business = Business::find($bizId);
        if ($business) {
            if ($langId == 1 || ($metaTitleVal === null && $metaDescVal === null)) {
                $business->meta_title = $metaTitleVal;
                $business->meta_description = $metaDescVal;
                $business->save();
            }
        }

        return $bizTrans;
    }

    /**
     * Helper to update or create FeatureTransalte meta fields.
     */
    protected function saveFeatureTranslationMeta(int $featId, int $langId, ?string $metaTitle, ?string $metaDesc)
    {
        $featTrans = FeatureTransalte::where('feature_id', $featId)
            ->where('lang_id', $langId)
            ->first();

        if ($featTrans) {
            $featTrans->meta_title = $metaTitle;
            $featTrans->meta_description = $metaDesc;
            $featTrans->save();
            return $featTrans;
        }

        $engTrans = FeatureTransalte::where('feature_id', $featId)->where('lang_id', 1)->first();

        return FeatureTransalte::create([
            'feature_id' => $featId,
            'lang_id' => $langId,
            'name' => $engTrans?->name ?? ('Feature #' . $featId),
            'description' => $engTrans?->description ?? '',
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
        ]);
    }

    // ==========================================
    // SHARED BUSINESS SUBPAGE HELPERS
    // ==========================================
    protected function renderBusinessSubpageView(Request $request, string $entityType, string $viewName, string $resolverMethod)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));

        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $lang = $selectedLanguage ? $selectedLanguage->lang_code : 'en-us';
        session()->put('lang_id', $lang_id);
        session()->put('lang_code', $lang);

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        $rule = MetaTagRule::getRuleFor($entityType, $lang_id);

        $businesses = Business::with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'category.translations' => fn($q) => $q->where('lang_id', $lang_id),
        ])->orderBy('id', 'asc')->get();

        foreach ($businesses as $business) {
            $business->metaData = MetaTagService::$resolverMethod($business, $lang_id, $rule);
        }

        return view($viewName, compact(
            'businesses',
            'rule',
            'languages',
            'lang_id',
            'selectedLanguage'
        ));
    }

    protected function updateSingleBusinessSubpageMeta(Request $request, string $titleCol, string $descCol, string $resolverMethod)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'lang_id' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $bizId = (int) $request->input('business_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $metaTitle = $request->input('meta_title', $request->input('title'));
        $metaDesc = $request->input('meta_description', $request->input('description'));

        $this->saveBusinessTranslationSubpageMeta($bizId, $langId, $titleCol, $descCol, $metaTitle, $metaDesc);

        $business = Business::find($bizId);
        $resolved = MetaTagService::$resolverMethod($business, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Meta tags updated successfully.',
            'resolved' => $resolved
        ]);
    }

    protected function resetBusinessSubpageMeta(Request $request, string $titleCol, string $descCol, string $resolverMethod)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'lang_id' => 'nullable|integer',
        ]);

        $bizId = (int) $request->input('business_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));

        $bizTrans = BusinessTranslation::where('business_id', $bizId)
            ->where('lang_id', $langId)
            ->first();

        if ($bizTrans) {
            $bizTrans->{$titleCol} = null;
            $bizTrans->{$descCol} = null;
            $bizTrans->save();
        }

        $business = Business::find($bizId);
        $resolved = MetaTagService::$resolverMethod($business, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Reset to auto dynamic default successfully.',
            'resolved' => $resolved
        ]);
    }

    protected function updateBulkBusinessSubpageMeta(Request $request, string $titleCol, string $descCol, string $routeName)
    {
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $titles = $request->input('meta_title', []);
        $descriptions = $request->input('meta_description', []);

        $allBizIds = array_unique(array_merge(array_keys($titles), array_keys($descriptions)));

        foreach ($allBizIds as $bizId) {
            if (!is_numeric($bizId)) continue;

            $metaTitle = $titles[$bizId] ?? null;
            $metaDesc = $descriptions[$bizId] ?? null;

            $this->saveBusinessTranslationSubpageMeta((int) $bizId, $langId, $titleCol, $descCol, $metaTitle, $metaDesc);
        }

        return redirect()->route($routeName, ['lang_id' => $langId])
            ->with('success', 'All meta tags updated successfully.');
    }

    protected function saveBusinessTranslationSubpageMeta(int $bizId, int $langId, string $titleCol, string $descCol, ?string $metaTitle, ?string $metaDesc)
    {
        $metaTitle = $metaTitle !== null ? trim($metaTitle) : null;
        $metaDesc = $metaDesc !== null ? trim($metaDesc) : null;

        $metaTitleVal = ($metaTitle !== '' && $metaTitle !== null) ? $metaTitle : null;
        $metaDescVal = ($metaDesc !== '' && $metaDesc !== null) ? $metaDesc : null;

        $bizTrans = BusinessTranslation::where('business_id', $bizId)
            ->where('lang_id', $langId)
            ->first();

        if ($bizTrans) {
            $bizTrans->{$titleCol} = $metaTitleVal;
            $bizTrans->{$descCol} = $metaDescVal;
            $bizTrans->save();
        } else {
            $biz = Business::find($bizId);
            $defaultName = $biz ? ($biz->name ?? "Business #$bizId") : "Business #$bizId";
            BusinessTranslation::create([
                'business_id' => $bizId,
                'lang_id' => $langId,
                'name' => $defaultName,
                'slug' => \Illuminate\Support\Str::slug($defaultName),
                $titleCol => $metaTitleVal,
                $descCol => $metaDescVal,
            ]);
        }
    }

    // ==========================================
    // 1. BUSINESS ALTERNATIVES
    // ==========================================
    public function businessAlternatives(Request $request)
    {
        return $this->renderBusinessSubpageView($request, 'business_alternatives', 'Admin.meta-tags.business-alternatives', 'resolveBusinessAlternativesMeta');
    }

    public function updateSingleBusinessAlternativesMeta(Request $request)
    {
        return $this->updateSingleBusinessSubpageMeta($request, 'alternatives_meta_title', 'alternatives_meta_description', 'resolveBusinessAlternativesMeta');
    }

    public function resetBusinessAlternativesMeta(Request $request)
    {
        return $this->resetBusinessSubpageMeta($request, 'alternatives_meta_title', 'alternatives_meta_description', 'resolveBusinessAlternativesMeta');
    }

    public function updateBulkBusinessAlternativesMeta(Request $request)
    {
        return $this->updateBulkBusinessSubpageMeta($request, 'alternatives_meta_title', 'alternatives_meta_description', 'admin.meta-tags.business-alternatives');
    }

    // ==========================================
    // 2. BUSINESS FAQS
    // ==========================================
    public function businessFaqs(Request $request)
    {
        return $this->renderBusinessSubpageView($request, 'business_faqs', 'Admin.meta-tags.business-faqs', 'resolveBusinessFaqsMeta');
    }

    public function updateSingleBusinessFaqsMeta(Request $request)
    {
        return $this->updateSingleBusinessSubpageMeta($request, 'faqs_meta_title', 'faqs_meta_description', 'resolveBusinessFaqsMeta');
    }

    public function resetBusinessFaqsMeta(Request $request)
    {
        return $this->resetBusinessSubpageMeta($request, 'faqs_meta_title', 'faqs_meta_description', 'resolveBusinessFaqsMeta');
    }

    public function updateBulkBusinessFaqsMeta(Request $request)
    {
        return $this->updateBulkBusinessSubpageMeta($request, 'faqs_meta_title', 'faqs_meta_description', 'admin.meta-tags.business-faqs');
    }

    // ==========================================
    // 3. BUSINESS COMPARE
    // ==========================================
    public function businessCompare(Request $request)
    {
        return $this->renderBusinessSubpageView($request, 'business_compare', 'Admin.meta-tags.business-compare', 'resolveBusinessCompareMeta');
    }

    public function updateSingleBusinessCompareMeta(Request $request)
    {
        return $this->updateSingleBusinessSubpageMeta($request, 'comparison_meta_title', 'comparison_meta_description', 'resolveBusinessCompareMeta');
    }

    public function resetBusinessCompareMeta(Request $request)
    {
        return $this->resetBusinessSubpageMeta($request, 'comparison_meta_title', 'comparison_meta_description', 'resolveBusinessCompareMeta');
    }

    public function updateBulkBusinessCompareMeta(Request $request)
    {
        return $this->updateBulkBusinessSubpageMeta($request, 'comparison_meta_title', 'comparison_meta_description', 'admin.meta-tags.business-compare');
    }

    // ==========================================
    // 4. BUSINESS REVIEWS
    // ==========================================
    public function businessReviews(Request $request)
    {
        return $this->renderBusinessSubpageView($request, 'business_reviews', 'Admin.meta-tags.business-reviews', 'resolveBusinessReviewsMeta');
    }

    public function updateSingleBusinessReviewsMeta(Request $request)
    {
        return $this->updateSingleBusinessSubpageMeta($request, 'reviews_meta_title', 'reviews_meta_description', 'resolveBusinessReviewsMeta');
    }

    public function resetBusinessReviewsMeta(Request $request)
    {
        return $this->resetBusinessSubpageMeta($request, 'reviews_meta_title', 'reviews_meta_description', 'resolveBusinessReviewsMeta');
    }

    public function updateBulkBusinessReviewsMeta(Request $request)
    {
        return $this->updateBulkBusinessSubpageMeta($request, 'reviews_meta_title', 'reviews_meta_description', 'admin.meta-tags.business-reviews');
    }

    // ==========================================
    // 5. BUSINESS DISCUSSIONS
    // ==========================================
    public function businessDiscussions(Request $request)
    {
        return $this->renderBusinessSubpageView($request, 'business_discussions', 'Admin.meta-tags.business-discussions', 'resolveBusinessDiscussionsMeta');
    }

    public function updateSingleBusinessDiscussionsMeta(Request $request)
    {
        return $this->updateSingleBusinessSubpageMeta($request, 'discussions_meta_title', 'discussions_meta_description', 'resolveBusinessDiscussionsMeta');
    }

    public function resetBusinessDiscussionsMeta(Request $request)
    {
        return $this->resetBusinessSubpageMeta($request, 'discussions_meta_title', 'discussions_meta_description', 'resolveBusinessDiscussionsMeta');
    }

    public function updateBulkBusinessDiscussionsMeta(Request $request)
    {
        return $this->updateBulkBusinessSubpageMeta($request, 'discussions_meta_title', 'discussions_meta_description', 'admin.meta-tags.business-discussions');
    }

    // ==========================================
    // 6. CATEGORY DISCUSSIONS
    // ==========================================
    public function categoryDiscussions(Request $request)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));

        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $lang = $selectedLanguage ? $selectedLanguage->lang_code : 'en-us';
        session()->put('lang_id', $lang_id);
        session()->put('lang_code', $lang);

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        $rule = MetaTagRule::getRuleFor('category_discussions', $lang_id);

        $parentCategories = Category::whereNull('parent_id')
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->get();

        foreach ($parentCategories as $parent) {
            $parent->metaData = MetaTagService::resolveCategoryDiscussionsMeta($parent, $lang_id, $rule);
            foreach ($parent->subCategories as $sub) {
                $sub->metaData = MetaTagService::resolveCategoryDiscussionsMeta($sub, $lang_id, $rule);
            }
        }

        return view('Admin.meta-tags.category-discussions', compact(
            'parentCategories',
            'rule',
            'languages',
            'lang_id',
            'selectedLanguage'
        ));
    }

    public function updateSingleCategoryDiscussionsMeta(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'lang_id' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $catId = (int) $request->input('category_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $metaTitle = $request->input('meta_title', $request->input('title'));
        $metaDesc = $request->input('meta_description', $request->input('description'));

        $this->saveCategoryTranslationDiscussionsMeta($catId, $langId, $metaTitle, $metaDesc);

        $category = Category::find($catId);
        $resolved = MetaTagService::resolveCategoryDiscussionsMeta($category, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Category Discussion meta tags updated successfully.',
            'resolved' => $resolved
        ]);
    }

    public function resetCategoryDiscussionsMeta(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'lang_id' => 'nullable|integer',
        ]);

        $catId = (int) $request->input('category_id');
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));

        $catTrans = CategoryTranslation::where('category_id', $catId)
            ->where('lang_id', $langId)
            ->first();

        if ($catTrans) {
            $catTrans->discussions_meta_title = null;
            $catTrans->discussions_meta_description = null;
            $catTrans->save();
        }

        $category = Category::find($catId);
        $resolved = MetaTagService::resolveCategoryDiscussionsMeta($category, $langId);

        return response()->json([
            'success' => true,
            'message' => 'Category Discussion reset to auto dynamic default successfully.',
            'resolved' => $resolved
        ]);
    }

    public function updateBulkCategoryDiscussionsMeta(Request $request)
    {
        $langId = (int) ($request->input('lang_id') ?: session()->get('lang_id', 1));
        $titles = $request->input('meta_title', []);
        $descriptions = $request->input('meta_description', []);

        $allCatIds = array_unique(array_merge(array_keys($titles), array_keys($descriptions)));

        foreach ($allCatIds as $catId) {
            if (!is_numeric($catId)) continue;

            $metaTitle = $titles[$catId] ?? null;
            $metaDesc = $descriptions[$catId] ?? null;

            $this->saveCategoryTranslationDiscussionsMeta((int) $catId, $langId, $metaTitle, $metaDesc);
        }

        return redirect()->route('admin.meta-tags.category-discussions', ['lang_id' => $langId])
            ->with('success', 'All category discussion meta tags updated successfully.');
    }

    protected function saveCategoryTranslationDiscussionsMeta(int $catId, int $langId, ?string $metaTitle, ?string $metaDesc)
    {
        $metaTitle = $metaTitle !== null ? trim($metaTitle) : null;
        $metaDesc = $metaDesc !== null ? trim($metaDesc) : null;

        $catTrans = CategoryTranslation::where('category_id', $catId)
            ->where('lang_id', $langId)
            ->first();

        if ($catTrans) {
            $catTrans->discussions_meta_title = ($metaTitle !== '' && $metaTitle !== null) ? $metaTitle : null;
            $catTrans->discussions_meta_description = ($metaDesc !== '' && $metaDesc !== null) ? $metaDesc : null;
            $catTrans->save();
            return $catTrans;
        }

        $engTrans = CategoryTranslation::where('category_id', $catId)->where('lang_id', 1)->first()
            ?? CategoryTranslation::where('category_id', $catId)->first();

        return CategoryTranslation::create([
            'category_id' => $catId,
            'lang_id' => $langId,
            'name' => $engTrans ? $engTrans->name : "Category #$catId",
            'slug' => $engTrans ? $engTrans->slug : "category-$catId",
            'discussions_meta_title' => ($metaTitle !== '' && $metaTitle !== null) ? $metaTitle : null,
            'discussions_meta_description' => ($metaDesc !== '' && $metaDesc !== null) ? $metaDesc : null,
        ]);
    }
}
