<?php

namespace App\Http\Controllers\Admin\ContentTemplates;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\ContentTemplate;
use App\Models\ContentTemplateAssignment;
use App\Models\Feature;
use App\Models\Language;
use App\Services\ContentTemplateService;
use Illuminate\Http\Request;

class ContentTemplateController extends Controller
{
    /**
     * Category Content Templates management & overview.
     */
    public function categories(Request $request)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));
        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        ContentTemplateService::ensureTemplatesExistForLanguage('category', $lang_id);

        $parentCategories = Category::whereNull('parent_id')
            ->with([
                'categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['categoryTranslations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->orderBy('id', 'asc')
            ->get();

        // Load all category assignments for this language
        $assignments = ContentTemplateAssignment::where('entity_type', 'category')
            ->where('lang_id', $lang_id)
            ->whereHas('template', fn($q) => $q->where('section_group', $sectionGroup))
            ->with('template')
            ->get()
            ->groupBy('entity_id');

        // Load all templates for category section
        $templates = ContentTemplate::where('section_group', 'category')
            ->where('lang_id', $lang_id)
            ->orderBy('field_key')
            ->orderBy('option_number')
            ->get()
            ->groupBy('field_key');

        $fieldDefs = ContentTemplateService::CATEGORY_FIELDS;

        return view('Admin.content-templates.categories', compact(
            'parentCategories',
            'assignments',
            'templates',
            'fieldDefs',
            'languages',
            'lang_id',
            'selectedLanguage'
        ));
    }

    /**
     * Category FAQs Content Templates management & overview.
     */
    public function categoriesFaqs(Request $request)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));
        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        ContentTemplateService::ensureTemplatesExistForLanguage('category', $lang_id);

        $parentCategories = Category::whereNull('parent_id')
            ->with([
                'categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['categoryTranslations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->orderBy('id', 'asc')
            ->get();

        $assignments = ContentTemplateAssignment::where('entity_type', 'category')
            ->where('lang_id', $lang_id)
            ->with('template')
            ->get()
            ->groupBy('entity_id');

        $templates = ContentTemplate::where('section_group', 'category_faqs')
            ->where('lang_id', $lang_id)
            ->orderBy('field_key')
            ->orderBy('option_number')
            ->get()
            ->groupBy('field_key');

        $fieldDefs = ContentTemplateService::CATEGORY_FAQ_FIELDS;

        return view('Admin.content-templates.categories-faqs', compact(
            'parentCategories',
            'assignments',
            'templates',
            'fieldDefs',
            'languages',
            'lang_id',
            'selectedLanguage'
        ));
    }

    /**
     * Features Content Templates management & overview.
     */
    public function features(Request $request)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));
        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        ContentTemplateService::ensureTemplatesExistForLanguage('features', $lang_id);

        $query = Feature::with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'category.categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id),
        ]);

        if ($categoryId = $request->get('category_id')) {
            $selectedCat = Category::find($categoryId);
            if ($selectedCat && $selectedCat->parent_id === null) {
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

        $allCategories = Category::whereNull('parent_id')
            ->with([
                'categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['categoryTranslations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->orderBy('id', 'asc')
            ->get();

        $assignments = ContentTemplateAssignment::where('entity_type', 'feature')
            ->where('lang_id', $lang_id)
            ->with('template')
            ->get()
            ->groupBy('entity_id');

        $templates = ContentTemplate::where('section_group', 'features')
            ->where('lang_id', $lang_id)
            ->orderBy('field_key')
            ->orderBy('option_number')
            ->get()
            ->groupBy('field_key');

        $fieldDefs = ContentTemplateService::FEATURE_FIELDS;

        return view('Admin.content-templates.features', compact(
            'features',
            'allCategories',
            'assignments',
            'templates',
            'fieldDefs',
            'languages',
            'lang_id',
            'selectedLanguage'
        ));
    }

    /**
     * Store a new template variation.
     */

    public function categoryDiscussions(Request $request) { return $this->genericCategoryView($request, 'category_discussions', \App\Services\ContentTemplateService::CATEGORY_DISCUSSIONS_FIELDS, 'Category Discussions'); }
    public function businessProfile(Request $request) { return $this->genericBusinessView($request, 'business_profile', \App\Services\ContentTemplateService::BUSINESS_PROFILE_FIELDS, 'Business Profile'); }
    public function businessAlternatives(Request $request) { return $this->genericBusinessView($request, 'business_alternatives', \App\Services\ContentTemplateService::BUSINESS_ALTERNATIVES_FIELDS, 'Business Alternatives'); }
    public function businessFaqs(Request $request) { return $this->genericBusinessView($request, 'business_faqs', \App\Services\ContentTemplateService::BUSINESS_FAQS_FIELDS, 'Business FAQs'); }
    public function businessCompare(Request $request) { return $this->genericBusinessView($request, 'business_compare', \App\Services\ContentTemplateService::BUSINESS_COMPARE_FIELDS, 'Business Compare'); }
    public function businessReviews(Request $request) { return $this->genericBusinessView($request, 'business_reviews', \App\Services\ContentTemplateService::BUSINESS_REVIEWS_FIELDS, 'Business Reviews'); }
    public function businessDiscussions(Request $request) { return $this->genericBusinessView($request, 'business_discussions', \App\Services\ContentTemplateService::BUSINESS_DISCUSSIONS_FIELDS, 'Business Discussions'); }

    private function genericCategoryView(Request $request, $sectionGroup, $fieldDefs, $title)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));
        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        ContentTemplateService::ensureTemplatesExistForLanguage($sectionGroup, $lang_id);

        $parentCategories = Category::whereNull('parent_id')
            ->with([
                'categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['categoryTranslations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->orderBy('id', 'asc')
            ->get();

        $assignments = ContentTemplateAssignment::where('entity_type', 'category')
            ->where('lang_id', $lang_id)
            ->with('template')
            ->get()
            ->groupBy('entity_id');

        $templates = ContentTemplate::where('section_group', $sectionGroup)
            ->where('lang_id', $lang_id)
            ->orderBy('field_key')
            ->orderBy('option_number')
            ->get()
            ->groupBy('field_key');

        return view('Admin.content-templates.generic-category', compact(
            'parentCategories',
            'assignments',
            'templates',
            'fieldDefs',
            'languages',
            'lang_id',
            'selectedLanguage',
            'title',
            'sectionGroup'
        ));
    }

    private function genericBusinessView(Request $request, $sectionGroup, $fieldDefs, $title)
    {
        $lang_id = (int) $request->get('lang_id', session('lang_id', 1));
        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('lang_code', 'en-us')->first() ?? Language::first();
            $lang_id = $selectedLanguage ? $selectedLanguage->id : 1;
        }

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        ContentTemplateService::ensureTemplatesExistForLanguage($sectionGroup, $lang_id);

        $query = Business::with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'category.categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id),
            'subCategories.categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id),
        ]);

        if ($categoryId = $request->get('category_id')) {
            $selectedCat = Category::find($categoryId);
            if ($selectedCat && $selectedCat->parent_id === null) {
                $subIds = $selectedCat->subCategories()->pluck('id')->push((int)$categoryId)->toArray();
                $query->where(function($q) use ($subIds) {
                    $q->whereIn('category_id', $subIds)
                      ->orWhereHas('subCategories', function($sq) use ($subIds) {
                          $sq->whereIn('categories.id', $subIds);
                      });
                });
            } else {
                $query->where(function($q) use ($categoryId) {
                    $q->where('category_id', $categoryId)
                      ->orWhereHas('subCategories', function($sq) use ($categoryId) {
                          $sq->where('categories.id', $categoryId);
                      });
                });
            }
        }

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($tq) use ($search) {
                    $tq->where('name', 'like', "%{$search}%");
                })->orWhere('permanent_url', 'like', "%{$search}%");
            });
        }

        $businesses = $query->orderBy('id', 'asc')->paginate(50)->withQueryString();

        $allCategories = Category::whereNull('parent_id')
            ->with([
                'categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id),
                'subCategories' => function($q) use ($lang_id) {
                    $q->with(['categoryTranslations' => fn($t) => $t->where('lang_id', $lang_id)]);
                }
            ])
            ->orderBy('id', 'asc')
            ->get();

        $assignments = ContentTemplateAssignment::where('entity_type', 'business')
            ->where('lang_id', $lang_id)
            ->whereHas('template', fn($q) => $q->where('section_group', $sectionGroup))
            ->with('template')
            ->get()
            ->groupBy('entity_id');

        $templates = ContentTemplate::where('section_group', $sectionGroup)
            ->where('lang_id', $lang_id)
            ->orderBy('field_key')
            ->orderBy('option_number')
            ->get()
            ->groupBy('field_key');

        return view('Admin.content-templates.generic-business', compact(
            'businesses',
            'allCategories',
            'assignments',
            'templates',
            'fieldDefs',
            'languages',
            'lang_id',
            'selectedLanguage',
            'title',
            'sectionGroup'
        ));
    }



    public function criteriaRatings(Request $request)
    {
        $lang_id = $request->get('lang_id') ? (int) $request->get('lang_id') : 1;
        $selectedLanguage = Language::find($lang_id);
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('status', 1)->orderBy('id', 'asc')->first() ?? Language::first();
            $lang_id = $selectedLanguage?->id ?? 1;
        }

        $languages = Language::where('status', 1)->orderBy('name', 'asc')->get();
        if ($languages->isEmpty()) {
            $languages = Language::all();
        }

        $allCriteria = \App\Models\DefaultRatingCriteria::orderBy('sort_order', 'asc')->get();
        $activeCriteriaId = (int) $request->get('criteria_id', $allCriteria->first()->id ?? 1);
        $activeCategoryId = (int) $request->get('category_id', 0);

        // Fetch parent categories with subcategories
        $categories = \App\Models\Category::with([
            'categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id)->orWhere('lang_id', 1),
            'subCategories.categoryTranslations' => fn($q) => $q->where('lang_id', $lang_id)->orWhere('lang_id', 1)
        ])
        ->whereNull('parent_id')
        ->orderBy('id', 'asc')
        ->get();

        $activeCategory = null;
        if ($activeCategoryId > 0) {
            $activeCategory = \App\Models\Category::with([
                'categoryTranslations',
                'parent.categoryTranslations'
            ])->find($activeCategoryId);
        }

        $sectionGroup = $activeCategoryId > 0
            ? ('criteria_ratings_' . $activeCriteriaId . '_cat_' . $activeCategoryId)
            : ('criteria_ratings_' . $activeCriteriaId);

        $fieldDefs = [];
        $templates = collect();

        if ($activeCriteriaId) {
            \App\Services\ContentTemplateService::ensureTemplatesExistForLanguage($sectionGroup, $lang_id);
            $fieldDefs = \App\Services\ContentTemplateService::getCriteriaFields($activeCriteriaId);
            
            $templates = ContentTemplate::where('section_group', $sectionGroup)
                ->where('lang_id', $lang_id)
                ->orderBy('field_key')
                ->orderBy('option_number')
                ->get()
                ->groupBy('field_key');
        }

        // Inheritance status info
        $hasCustom = $templates->isNotEmpty() && $templates->flatten()->count() > 0;
        $inheritanceInfo = [
            'has_custom_templates' => $hasCustom,
            'source_label'         => '',
            'source_section_group' => '',
            'can_copy'             => false,
            'inherited_count'      => 0,
        ];

        if ($activeCategoryId > 0 && !$hasCustom) {
            if ($activeCategory && $activeCategory->parent_id) {
                // Subcategory: check parent category templates first
                $parentSection = 'criteria_ratings_' . $activeCriteriaId . '_cat_' . $activeCategory->parent_id;
                $parentTemplates = ContentTemplate::where('section_group', $parentSection)->where('lang_id', $lang_id)->get();
                if ($parentTemplates->isEmpty() && $lang_id !== 1) {
                    $parentTemplates = ContentTemplate::where('section_group', $parentSection)->where('lang_id', 1)->get();
                }

                $parentTrans = $activeCategory->parent?->categoryTranslations?->where('lang_id', $lang_id)->first()
                    ?? $activeCategory->parent?->categoryTranslations?->first();
                $parentName = $parentTrans?->name ?? $activeCategory->parent?->name ?? 'Parent Category';

                if ($parentTemplates->isNotEmpty()) {
                    $inheritanceInfo['source_label'] = "Parent Category: {$parentName}";
                    $inheritanceInfo['source_section_group'] = $parentSection;
                    $inheritanceInfo['can_copy'] = true;
                    $inheritanceInfo['inherited_count'] = $parentTemplates->count();
                } else {
                    $globalSection = 'criteria_ratings_' . $activeCriteriaId;
                    $globalTemplates = ContentTemplate::where('section_group', $globalSection)->where('lang_id', $lang_id)->get();
                    if ($globalTemplates->isEmpty() && $lang_id !== 1) {
                        $globalTemplates = ContentTemplate::where('section_group', $globalSection)->where('lang_id', 1)->get();
                    }
                    $inheritanceInfo['source_label'] = "Global Defaults";
                    $inheritanceInfo['source_section_group'] = $globalSection;
                    $inheritanceInfo['can_copy'] = $globalTemplates->isNotEmpty();
                    $inheritanceInfo['inherited_count'] = $globalTemplates->count();
                }
            } else {
                // Parent category: check global defaults
                $globalSection = 'criteria_ratings_' . $activeCriteriaId;
                $globalTemplates = ContentTemplate::where('section_group', $globalSection)->where('lang_id', $lang_id)->get();
                if ($globalTemplates->isEmpty() && $lang_id !== 1) {
                    $globalTemplates = ContentTemplate::where('section_group', $globalSection)->where('lang_id', 1)->get();
                }
                $inheritanceInfo['source_label'] = "Global Defaults";
                $inheritanceInfo['source_section_group'] = $globalSection;
                $inheritanceInfo['can_copy'] = $globalTemplates->isNotEmpty();
                $inheritanceInfo['inherited_count'] = $globalTemplates->count();
            }
        }

        return view('Admin.content-templates.criteria-ratings', compact(
            'allCriteria',
            'activeCriteriaId',
            'categories',
            'activeCategoryId',
            'activeCategory',
            'inheritanceInfo',
            'templates',
            'fieldDefs',
            'languages',
            'lang_id',
            'selectedLanguage',
            'sectionGroup'
        ));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'section_group' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $standard = [
                        'category', 'category_faqs', 'category_discussions',
                        'business_profile', 'business_alternatives', 'business_faqs',
                        'business_compare', 'business_reviews', 'business_discussions',
                        'features', 'criteria_ratings'
                    ];
                    if (in_array($value, $standard) || str_starts_with($value, 'criteria_ratings_')) {
                        return true;
                    }
                    $fail("The selected {$attribute} is invalid.");
                }
            ],
            'field_key'     => 'required|string|max:64',
            'headline'      => 'nullable|string',
            'subtext'       => 'nullable|string',
            'lang_id'       => 'nullable|integer',
        ]);

        $langId = (int) ($request->lang_id ?: 1);

        $maxOption = ContentTemplate::where('section_group', $request->section_group)
            ->where('field_key', $request->field_key)
            ->where('lang_id', $langId)
            ->max('option_number') ?? 0;

        $headline = $request->headline !== null
            ? trim(strip_tags(html_entity_decode($request->headline, ENT_QUOTES, 'UTF-8')))
            : null;

        $template = ContentTemplate::create([
            'section_group' => $request->section_group,
            'field_key'     => $request->field_key,
            'option_number' => $maxOption + 1,
            'headline'      => $headline,
            'subtext'       => $request->subtext,
            'lang_id'       => $langId,
            'is_active'     => true,
        ]);

        return redirect()->back()->with('success', "Template Option #{$template->option_number} created successfully.");
    }

    /**
     * Update an existing template variation.
     */
    public function updateTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|integer|exists:content_templates,id',
            'headline'    => 'nullable|string',
            'subtext'     => 'nullable|string',
        ]);

        $headline = $request->headline !== null
            ? trim(strip_tags(html_entity_decode($request->headline, ENT_QUOTES, 'UTF-8')))
            : null;

        $template = ContentTemplate::findOrFail($request->template_id);
        $template->update([
            'headline' => $headline,
            'subtext'  => $request->subtext,
        ]);

        return redirect()->back()->with('success', "Template Option #{$template->option_number} updated successfully.");
    }

    /**
     * Delete a template variation.
     */
    public function deleteTemplate($id)
    {
        $template = ContentTemplate::findOrFail($id);
        $template->delete();

        return redirect()->back()->with('success', "Template Option #{$template->option_number} deleted successfully.");
    }

    /**
     * Randomly assign and permanently lock missing templates for all unassigned entities.
     */
    public function assignMissing(Request $request)
    {
        $section = $request->get('section', 'category');
        $langId = (int) ($request->get('lang_id') ?: 1);

        if ($section === 'features') {
            $assignedCount = ContentTemplateService::assignMissingForFeatures($langId);
        } elseif (in_array($section, ['business_profile', 'business_alternatives', 'business_faqs', 'business_compare', 'business_reviews', 'business_discussions'])) {
            $assignedCount = ContentTemplateService::assignMissingForBusinesses($langId, $section);
        } else {
            $assignedCount = ContentTemplateService::assignMissingForCategories($langId, $section);
        }

        return redirect()->back()->with('success', "Successfully assigned and locked {$assignedCount} template variations permanently.");
    }

    /**
     * Manually assign a template option to an entity.
     */
    public function assignSingle(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|string|in:category,feature,business',
            'entity_id'   => 'required|integer',
            'field_key'   => 'required|string',
            'template_id' => 'required|integer|exists:content_templates,id',
            'lang_id'     => 'nullable|integer',
        ]);

        $langId = (int) ($request->lang_id ?: 1);

        ContentTemplateService::manuallyAssignTemplate(
            $request->entity_type,
            (int)$request->entity_id,
            $request->field_key,
            (int)$request->template_id,
            $langId
        );

        return response()->json([
            'success' => true,
            'message' => 'Assignment updated and locked successfully.',
        ]);
    }

    /**
     * Reset/clear locked template assignments for a specific entity (category/feature/business).
     */
    public function resetEntity(Request $request)
    {
        $request->validate([
            'entity_type'   => 'required|string|in:category,feature,business',
            'entity_id'     => 'required|integer',
            'section_group' => 'nullable|string|in:category,category_faqs,category_discussions,business_profile,business_alternatives,business_faqs,business_compare,business_reviews,business_discussions,features,criteria_ratings',
            'lang_id'       => 'nullable|integer',
        ]);

        $langId = (int) ($request->lang_id ?: 1);
        $entityType = $request->entity_type;
        $entityId = (int) $request->entity_id;
        $sectionGroup = $request->section_group;

        $query = ContentTemplateAssignment::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('lang_id', $langId);

        if (str_starts_with($sectionGroup, 'criteria_ratings_')) {
            $critId = (int) str_replace('criteria_ratings_', '', $sectionGroup);
            $query->whereIn('field_key', array_keys(ContentTemplateService::getCriteriaFields($critId)));
        } elseif ($sectionGroup === 'category') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::CATEGORY_FIELDS));
        } elseif ($sectionGroup === 'category_faqs') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::CATEGORY_FAQ_FIELDS));
        } elseif ($sectionGroup === 'category_discussions') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::CATEGORY_DISCUSSIONS_FIELDS));
        } elseif ($sectionGroup === 'business_profile') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::BUSINESS_PROFILE_FIELDS));
        } elseif ($sectionGroup === 'business_alternatives') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::BUSINESS_ALTERNATIVES_FIELDS));
        } elseif ($sectionGroup === 'business_faqs') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::BUSINESS_FAQS_FIELDS));
        } elseif ($sectionGroup === 'business_compare') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::BUSINESS_COMPARE_FIELDS));
        } elseif ($sectionGroup === 'business_reviews') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::BUSINESS_REVIEWS_FIELDS));
        } elseif ($sectionGroup === 'business_discussions') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::BUSINESS_DISCUSSIONS_FIELDS));
        } elseif ($sectionGroup === 'features') {
            $query->whereIn('field_key', array_keys(ContentTemplateService::FEATURE_FIELDS));
        }

        $deleted = $query->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Templates for this {$entityType} have been reset successfully.",
                'deleted' => $deleted
            ]);
        }

        return redirect()->back()->with('success', "Templates for this {$entityType} have been reset successfully.");
    }

    public function copyCriteriaTemplates(Request $request)
    {
        $request->validate([
            'from_section_group' => 'required|string',
            'to_section_group'   => 'required|string',
            'lang_id'            => 'required|integer',
            'criteria_id'        => 'nullable|integer',
            'category_id'        => 'nullable|integer',
        ]);

        $copied = ContentTemplateService::copyCriteriaTemplates(
            $request->from_section_group,
            $request->to_section_group,
            (int) $request->lang_id
        );

        return redirect()->route('admin.content-templates.criteria-ratings', [
            'criteria_id' => $request->criteria_id,
            'category_id' => $request->category_id,
            'lang_id'     => $request->lang_id,
        ])->with('success', "Copied {$copied} template variations successfully.");
    }
}
