<?php

namespace App\Http\Controllers\Admin\MetaTags;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
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
        // Accept lang_id from query string or session, fallback to 1 (English)
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

        $parentCategories = Category::onlyParents()
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->get();

        // Pre-resolve metadata for every parent and subcategory
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
        $rule->description_char_limit = $request->input('description_char_limit');
        $rule->description_primary = $request->input('description_primary');
        $rule->description_secondary = $request->input('description_secondary');
        $rule->description_tertiary = $request->input('description_tertiary');
        $rule->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Default meta tag rules updated successfully!',
                'rule' => $rule
            ]);
        }

        return redirect()->route('admin.meta-tags.categories', ['lang_id' => $langId])
            ->with('success', 'Default meta tag rules updated successfully!');
    }

    /**
     * Update single category manual override via AJAX.
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
     * Helper to update or create CategoryTranslation with a collision-free slug.
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
     * Placeholders for subsequent subcategories requested by user.
     */
    public function businesses()
    {
        return redirect()->route('admin.meta-tags.categories')->with('info', 'Businesses Meta Tags module will be available next.');
    }

    public function categoriesFaqs()
    {
        return redirect()->route('admin.meta-tags.categories')->with('info', 'Categories FAQs Meta Tags module will be available next.');
    }

    public function features()
    {
        return redirect()->route('admin.meta-tags.categories')->with('info', 'Features Meta Tags module will be available next.');
    }
}
