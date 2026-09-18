<?php

namespace App\Http\Controllers\Admin\ContentTemplates;

use App\Http\Controllers\Controller;
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
    public function businessProfile(Request $request) { return $this->genericCategoryView($request, 'business_profile', \App\Services\ContentTemplateService::BUSINESS_PROFILE_FIELDS, 'Business Profile'); }
    public function businessAlternatives(Request $request) { return $this->genericCategoryView($request, 'business_alternatives', \App\Services\ContentTemplateService::BUSINESS_ALTERNATIVES_FIELDS, 'Business Alternatives'); }
    public function businessFaqs(Request $request) { return $this->genericCategoryView($request, 'business_faqs', \App\Services\ContentTemplateService::BUSINESS_FAQS_FIELDS, 'Business FAQs'); }
    public function businessCompare(Request $request) { return $this->genericCategoryView($request, 'business_compare', \App\Services\ContentTemplateService::BUSINESS_COMPARE_FIELDS, 'Business Compare'); }
    public function businessReviews(Request $request) { return $this->genericCategoryView($request, 'business_reviews', \App\Services\ContentTemplateService::BUSINESS_REVIEWS_FIELDS, 'Business Reviews'); }
    public function businessDiscussions(Request $request) { return $this->genericCategoryView($request, 'business_discussions', \App\Services\ContentTemplateService::BUSINESS_DISCUSSIONS_FIELDS, 'Business Discussions'); }

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

        \App\Services\ContentTemplateService::ensureTemplatesExistForLanguage($sectionGroup, $lang_id);

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


    public function criteriaRatings(Request $request)
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

        $allCriteria = \App\Models\DefaultRatingCriteria::orderBy('sort_order', 'asc')->get();
        $activeCriteriaId = $request->get('criteria_id', $allCriteria->first()->id ?? 0);
        $sectionGroup = 'criteria_ratings_' . $activeCriteriaId;

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

        return view('Admin.content-templates.criteria-ratings', compact(
            'allCriteria',
            'activeCriteriaId',
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
            'section_group' => 'required|string|in:category,category_faqs,category_discussions,business_profile,business_alternatives,business_faqs,business_compare,business_reviews,business_discussions,features,criteria_ratings',
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

        $template = ContentTemplate::create([
            'section_group' => $request->section_group,
            'field_key'     => $request->field_key,
            'option_number' => $maxOption + 1,
            'headline'      => $request->headline,
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

        $template = ContentTemplate::findOrFail($request->template_id);
        $template->update([
            'headline' => $request->headline,
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
            'entity_type' => 'required|string|in:category,feature',
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
     * Reset/clear locked template assignments for a specific entity (category/feature).
     */
    public function resetEntity(Request $request)
    {
        $request->validate([
            'entity_type'   => 'required|string|in:category,feature',
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
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::getCriteriaFields($critId)));
        } elseif ($sectionGroup === 'category') {
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::CATEGORY_FIELDS));
        } elseif ($sectionGroup === 'category_faqs') {
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::CATEGORY_FAQ_FIELDS));
        } elseif ($sectionGroup === 'category_discussions') {
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::CATEGORY_DISCUSSIONS_FIELDS));
        } elseif ($sectionGroup === 'business_profile') {
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::BUSINESS_PROFILE_FIELDS));
        } elseif ($sectionGroup === 'business_alternatives') {
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::BUSINESS_ALTERNATIVES_FIELDS));
        } elseif ($sectionGroup === 'business_faqs') {
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::BUSINESS_FAQS_FIELDS));
        } elseif ($sectionGroup === 'business_compare') {
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::BUSINESS_COMPARE_FIELDS));
        } elseif ($sectionGroup === 'business_reviews') {
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::BUSINESS_REVIEWS_FIELDS));
        } elseif ($sectionGroup === 'business_discussions') {
            $query->whereIn('field_key', array_keys(\App\Services\ContentTemplateService::BUSINESS_DISCUSSIONS_FIELDS));
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
}