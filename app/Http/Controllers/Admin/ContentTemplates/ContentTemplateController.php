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
            ->keyBy('entity_id');

        $templates = ContentTemplate::where('section_group', 'features')
            ->where('lang_id', $lang_id)
            ->orderBy('option_number')
            ->get();

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
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'section_group' => 'required|string|in:category,category_faqs,features',
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
            $assignedCount = ContentTemplateService::assignMissingForCategories($langId);
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
}