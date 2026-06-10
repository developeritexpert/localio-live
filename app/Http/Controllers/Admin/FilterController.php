<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{Category, Language, CategoryTranslation, Filter, FilterOption, FilterTranslation, FilterOptionTranslation, FilterType};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FilterController extends Controller
{
    public function index(Request $request)
    {
        $lang_id = getCurrentLanguageID();

        $categories = Category::whereHas('translations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })
            ->with([
                'translations' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                },
                'filters' => function ($query) use ($lang_id) {
                    $query->whereHas('translations', function ($q) use ($lang_id) {
                        $q->where('language_id', $lang_id);
                    });
                },
                'filters.translations' => function ($query) use ($lang_id) {
                    $query->where('language_id', $lang_id);
                },
            ])
            ->get();

        $filters = Filter::whereHas('translations', function ($query) use ($lang_id) {
            $query->where('language_id', $lang_id);
        })
            ->with([
                'translations' => function ($query) use ($lang_id) {
                    $query->where('language_id', $lang_id);
                },
                'options.translations' => function ($query) use ($lang_id) {
                    $query->where('language_id', $lang_id);
                },
                'category' => function ($query) use ($lang_id) {
                    $query->whereHas('translations', function ($q) use ($lang_id) {
                        $q->where('lang_id', $lang_id);
                    });
                },
                'category.translations' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                },
            ])
            ->get();
        return view('Admin.filters.category_index', compact(['categories', 'filters']));
    }

    public function getFilters($id)
    {
        $filters = Filter::where('category_id', $id)->with(['options.translations', 'translations', 'category.translations'])->get();

        return view('Admin.filters.partials.duplicate_options', compact('filters'));
    }

    public function categoryfilters($id)
    {
        $lang_id = getCurrentLanguageID();
        $category = Category::where('id', $id)->with(relations: 'translations')->first();
        $filters = Filter::where('category_id', $id)->whereHas('translations', function ($query) use ($lang_id) {
            $query->where('language_id', $lang_id);
        })->with([
            'options.translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'category.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
        ])->get();
        return view('Admin.filters.index', compact('filters', 'category'));
    }
    public function add(Request $request, $id)
    {
        $lang_id = getCurrentLanguageID();
        $categories = Category::whereHas('translations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })->with('translations')->get();
        $filterTypes = FilterType::all();
        $category_id = $id;
        return view('Admin.filters.add', compact('category_id', 'categories', 'filterTypes'));
    }
    public function preview(Request $request)
    {
        $type = $request->filter_type_slug;
        // dd($request->all());
      

        $data = [
            'name' => $request->name,
            'options' => $request->options ?? [],
            'default_options' => $request->default_options ?? [],
            'min' => $request->min_value,
            'max' => $request->max_value,
            'unit' => $request->unit,
            'default_range' => $request->default_range,
            'on_label' => $request->on_label,
            'off_label' => $request->off_label,
            'default_toggle' => $request->default_toggle,
            'type' => $type,
        ];
        // dd($data);
        return view('Admin.filters.partials.preview', $data);
    }

    public function addProcc(Request $request)
    {
        $lang_id = getCurrentLanguageID();
       
        // Validate main filter if provided
        $mainFilterValidationRules = [];
        // dd($request->all());
        if ($request->filled('name') && $request->filled('category_id') && $request->filled('filter_type_id')) {
            $mainFilterValidationRules = [
                'name' => 'required|string',
                'slug' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'filter_type_id' => 'required|exists:filter_types,id',
                'is_required' => 'nullable',
            ];
            // dd($request->all());
            // Get filter type for type-specific validation
            $filterType = FilterType::find($request->filter_type_id);
            if ($filterType) {
                if ($filterType->slug === 'slider') {
                    $mainFilterValidationRules['min_value'] = 'required|numeric';
                    $mainFilterValidationRules['max_value'] = 'required|numeric|gt:min_value';
                    $mainFilterValidationRules['unit'] = 'nullable|string|max:50';
                    $mainFilterValidationRules['default_range'] = 'nullable|string';
                } elseif ($filterType->slug === 'toggle') {
                    $mainFilterValidationRules['on_label'] = 'nullable|string|max:50';
                    $mainFilterValidationRules['off_label'] = 'nullable|string|max:50';
                    $mainFilterValidationRules['default_toggle'] = 'nullable';
                } elseif (in_array($filterType->slug, ['dropdown', 'checkbox', 'radio'])) {
                    $mainFilterValidationRules['options'] = 'nullable|array';
                    $mainFilterValidationRules['options.*'] = 'nullable|string';
                    $mainFilterValidationRules['default_options'] = 'nullable|array';
                    $mainFilterValidationRules['default_options.*'] = 'nullable|string';
                }
            }
        }

        // Validate duplicated filters
        $filtersValidationRules = [];
        if ($request->has('filters')) {
            $filtersValidationRules['filters'] = 'array';
            $filtersValidationRules['filters.*.name'] = 'required|string';
            $filtersValidationRules['filters.*.slug'] = 'nullable|string';
            $filtersValidationRules['filters.*.category_id'] = 'required|exists:categories,id';
            $filtersValidationRules['filters.*.filter_type_id'] = 'required|exists:filter_types,id';
            $filtersValidationRules['filters.*.is_required'] = 'nullable';

            // For each filter, add type-specific validation rules
            foreach ($request->filters as $id => $filterData) {
                if (isset($filterData['filter_type_id'])) {
                    $filterType = FilterType::find($filterData['filter_type_id']);
                    if ($filterType) {
                        if ($filterType->slug === 'slider') {
                            $filtersValidationRules['filters.' . $id . '.min_value'] = 'required|numeric';
                            $filtersValidationRules['filters.' . $id . '.max_value'] = 'required|numeric|gt:filters.' . $id . '.min_value';
                            $filtersValidationRules['filters.' . $id . '.unit'] = 'nullable|string|max:50';
                            $filtersValidationRules['filters.' . $id . '.default_range'] = 'nullable|string';
                        } elseif ($filterType->slug === 'toggle') {
                            $filtersValidationRules['filters.' . $id . '.on_label'] = 'nullable|string|max:50';
                            $filtersValidationRules['filters.' . $id . '.off_label'] = 'nullable|string|max:50';
                            $filtersValidationRules['filters.' . $id . '.default_toggle'] = 'nullable';
                        } elseif (in_array($filterType->slug, ['dropdown', 'checkbox', 'radio'])) {
                            $filtersValidationRules['filters.' . $id . '.options'] = 'nullable|array';
                            $filtersValidationRules['filters.' . $id . '.options.*'] = 'nullable|string';
                            $filtersValidationRules['filters.' . $id . '.default_options'] = 'nullable|array';
                            $filtersValidationRules['filters.' . $id . '.default_options.*'] = 'nullable|string';
                        }
                    }
                }
            }
        }

        // Combine validation rules
        $validationRules = array_merge($mainFilterValidationRules, $filtersValidationRules);

        // Validate the request
        $validated = $request->validate($validationRules);

        // Initialize filters array
        $filtersData = [];

        // Add main filter if provided
        if ($request->filled('name') && $request->filled('category_id') && $request->filled('filter_type_id')) {
            $mainFilter = [
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'category_id' => $request->category_id,
                'filter_type_id' => $request->filter_type_id,
                'is_required' => $request->is_required ? 1 : 0,
            ];

            // Add type-specific data
            $filterType = FilterType::find($request->filter_type_id);
            if ($filterType) {
                if ($filterType->slug === 'slider') {
                    $mainFilter['min_value'] = $request->min_value;
                    $mainFilter['max_value'] = $request->max_value;
                    $mainFilter['unit'] = $request->unit;
                    $mainFilter['default_range'] = $request->default_range;
                } elseif ($filterType->slug === 'toggle') {
                    $mainFilter['on_label'] = $request->on_label ?: 'Yes';
                    $mainFilter['off_label'] = $request->off_label ?: 'No';
                    $mainFilter['default_toggle'] = $request->default_toggle ? 1 : 0;
                } elseif (in_array($filterType->slug, ['dropdown', 'checkbox', 'radio'])) {
                    $mainFilter['options'] = $request->options ?: [];
                    $mainFilter['default_options'] = $request->default_options ?: [];
                }
            }

            $filtersData[] = $mainFilter;
        }

        // Add duplicated filters
        if ($request->has('filters')) {
            foreach ($request->filters as $id => $filterData) {
                $typeId = $filterData['filter_type_id'];
                $filterType = FilterType::find($typeId);

                $filter = [
                    'name' => $filterData['name'],
                    'slug' => isset($filterData['slug']) && !empty($filterData['slug'])
                        ? $filterData['slug']
                        : Str::slug($filterData['name']),
                    'category_id' => $filterData['category_id'],
                    'filter_type_id' => $typeId,
                    'is_required' => isset($filterData['is_required']) ? 1 : 0,
                ];

                // Add type-specific data
                if ($filterType) {
                    if ($filterType->slug === 'slider') {
                        $filter['min_value'] = $filterData['min_value'] ?? 0;
                        $filter['max_value'] = $filterData['max_value'] ?? 100;
                        $filter['unit'] = $filterData['unit'] ?? '';
                        $filter['default_range'] = $filterData['default_range'] ?? null;
                    } elseif ($filterType->slug === 'toggle') {
                        $filter['on_label'] = $filterData['on_label'] ?? 'Yes';
                        $filter['off_label'] = $filterData['off_label'] ?? 'No';
                        $filter['default_toggle'] = isset($filterData['default_toggle']) ? 1 : 0;
                    } elseif (in_array($filterType->slug, ['dropdown', 'checkbox', 'radio'])) {
                        $filter['options'] = $filterData['options'] ?? [];
                        $filter['default_options'] = $filterData['default_options'] ?? [];
                    }
                }

                $filtersData[] = $filter;
            }
        }

        // Check if we have at least one filter to process
        if (empty($filtersData)) {
            return redirect()->back()->with('error', 'No valid filter data was provided.')->withInput();
        }

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Process each filter
            foreach ($filtersData as $filterData) {
                // Check if name exists — block creation
                $nameExists = Filter::where('category_id', $filterData['category_id'])
                    ->where('name', $filterData['name'])
                    ->exists();

                if ($nameExists) {
                    throw new \Exception("Filter with name '{$filterData['name']}' already exists for this category.");
                }

                // Check if slug exists — generate new slug
                $slugExists = Filter::where('category_id', $filterData['category_id'])
                    ->where('slug', $filterData['slug'])
                    ->exists();

                if ($slugExists) {
                    $originalSlug = $filterData['slug'];
                    $counter = 1;

                    do {
                        $newSlug = $originalSlug . '-' . $counter++;
                    } while (
                        Filter::where('category_id', $filterData['category_id'])
                        ->where('slug', $newSlug)
                        ->exists()
                    );

                    $filterData['slug'] = $newSlug;
                }

                $filterType = FilterType::findOrFail($filterData['filter_type_id']);
                $filterTypeSlug = $filterType->slug;

                // Create the filter
                $filter = new Filter();
                $filter->name = $filterData['name'];
                $filter->slug = $filterData['slug'];
                $filter->category_id = $filterData['category_id'];
                $filter->filter_type_id = $filterData['filter_type_id'];
                $filter->is_required = $filterData['is_required'] ?? 0;
                $filter->display_order = Filter::where('category_id', $filterData['category_id'])->max('display_order') + 1 ?? 1;
                $filter->save();

                // Create filter translation
                $translation = new FilterTranslation();
                $translation->filter_id = $filter->id;
                $translation->language_id = $lang_id;
                $translation->name = $filterData['name'];
                $translation->slug = $filterData['slug'];
                $translation->save();

                // Create type-specific filter options
                if ($filterTypeSlug === 'slider') {
                    $option = new FilterOption();
                    $option->filter_id = $filter->id;
                    $option->filter_type_id = $filter->filter_type_id;
                    $option->min_value = $filterData['min_value'] ?? 0;
                    $option->max_value = $filterData['max_value'] ?? 100;
                    $option->unit = $filterData['unit'] ?? '';
                    $option->default_range = $filterData['default_range'] ?? null;
                    $option->is_default = !empty($filterData['default_range']) ? true : false;
                    $option->save();

                    // Create option translation
                    $optionTrans = new FilterOptionTranslation();
                    $optionTrans->filter_option_id = $option->id;
                    $optionTrans->language_id = $lang_id;
                    $optionTrans->min_value = $filterData['min_value'] ?? 0;
                    $optionTrans->max_value = $filterData['max_value'] ?? 100;
                    $optionTrans->unit = $filterData['unit'] ?? '';
                    $optionTrans->default_range = $filterData['default_range'] ?? null;
                    $optionTrans->save();
                } elseif ($filterTypeSlug === 'toggle') {
                    $option = new FilterOption();
                    $option->filter_id = $filter->id;
                    $option->filter_type_id = $filter->filter_type_id;
                    $option->on_label = $filterData['on_label'] ?? 'Yes';
                    $option->off_label = $filterData['off_label'] ?? 'No';
                    $option->default_toggle = $filterData['default_toggle'] ?? 0;
                    $option->is_default = !empty($filterData['default_toggle']) ? true : false;
                    $option->save();

                    // Create option translation
                    $optionTrans = new FilterOptionTranslation();
                    $optionTrans->filter_option_id = $option->id;
                    $optionTrans->language_id = $lang_id;
                    $optionTrans->on_label = $filterData['on_label'] ?? 'Yes';
                    $optionTrans->off_label = $filterData['off_label'] ?? 'No';
                    $optionTrans->default_toggle = $filterData['default_toggle'] ?? 0;
                    $optionTrans->save();
                } elseif (in_array($filterTypeSlug, ['dropdown', 'checkbox', 'radio']) && !empty($filterData['options'])) {
                    // Get default options as array indices
                    $defaultOptions = !empty($filterData['default_options']) ? array_keys($filterData['default_options']) : [];

                    foreach ($filterData['options'] as $i => $optionName) {
                        if (empty($optionName)) continue;

                        $option = new FilterOption();
                        $option->filter_id = $filter->id;
                        $option->filter_type_id = $filter->filter_type_id;
                        $option->name = $optionName;

                        // Set is_default based on whether this option is in the default_options array
                        $option->is_default = in_array($i, $defaultOptions) ? true : false;

                        $option->save();

                        $optionTrans = new FilterOptionTranslation();
                        $optionTrans->filter_option_id = $option->id;
                        $optionTrans->language_id = $lang_id;
                        $optionTrans->name = $optionName;
                        $optionTrans->save();  // You could set this directly in the option record if you had a column for it
                        // Or create a separate default_options table with a relationship to filter_options
                    }
                }
            }
            DB::commit();

            return redirect()->route('filters')->with('success', 'Filters added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function updateFilterOrder(Request $request)
    {
        try {
            $filters = $request->input('filters');

            foreach ($filters as $item) {
                Filter::where('id', $item['id'])->update([
                    'display_order' => $item['position']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Filter order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating filter order: ' . $e->getMessage()
            ], 500);
        }
    }
    public function fetchFilters(Request $request)
    {
        $filters = Filter::with(['options', 'filterType'])
            ->whereIn('id', $request->filter_ids)
            ->get()
            ->map(function ($filter) {
                // Format the response to include all necessary data
                return [
                    'id' => $filter->id,
                    'name' => $filter->name,
                    'slug' => $filter->slug,
                    'category_id' => $filter->category_id,
                    'filter_type_id' => $filter->filter_type_id,
                    'filter_type' => [
                        'id' => $filter->filterType->id,
                        'name' => $filter->filterType->name,
                        'slug' => $filter->filterType->slug,
                    ],
                    'is_required' => $filter->is_required,
                    'min_value' => $filter->min_value,
                    'max_value' => $filter->max_value,
                    'unit' => $filter->unit,
                    'on_label' => $filter->on_label,
                    'off_label' => $filter->off_label,
                    'options' => $filter->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name
                        ];
                    })
                ];
            });

        return response()->json([
            'status' => true,
            'filters' => $filters
        ]);
    }
    public function updateFilter(Request $request, $filterId)
    {
        $lang_id = getCurrentLanguageID();
        $filter = Filter::with([
            'filterType',
            'options.translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'category.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
        ])->findOrFail($filterId);

        $categories = Category::with(['translations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])
        ->where('status', '1')
        ->whereHas('translations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })
        ->get();
        $filterTypes = FilterType::all();

        return view('Admin.filters.update', compact('filter', 'categories', 'filterTypes'));
    }

    public function updateProcc(Request $request)
    {
        $id = $request->input('id');
        $lang_id = getCurrentLanguageID();

        try {
            $filter = Filter::findOrFail($id);
            $originalFilterTypeId = $filter->filter_type_id;
            $filterType = FilterType::findOrFail($request->filter_type_id);
            $filterTypeSlug = $filterType->slug;

            // Validation rules
            $validationRules = [
                'name' => [
                    'required',
                    'string',
                    Rule::unique('filters')->where(function ($query) use ($request, $id) {
                        return $query->where('category_id', $request->category_id);
                    })->ignore($id),
                ],
                'slug' => 'nullable|string',
                'is_required' => 'nullable',
                'category_id' => 'required|exists:categories,id',
            ];

            // Type-specific validation rules
            if ($filterTypeSlug === 'slider') {
                $validationRules += [
                    'min_value' => 'required|numeric',
                    'max_value' => 'required|numeric|gt:min_value',
                    'unit' => 'nullable|string|max:50',
                    'default_range' => 'nullable|string',
                ];
            } elseif ($filterTypeSlug === 'toggle') {
                $validationRules += [
                    'on_label' => 'nullable|string|max:50',
                    'off_label' => 'nullable|string|max:50',
                    'default_toggle' => 'nullable',
                ];
            } elseif (in_array($filterTypeSlug, ['dropdown', 'checkbox', 'radio'])) {
                $validationRules += [
                    'options' => 'nullable|array',
                    'options.*' => 'nullable|string',
                    'default_options' => 'nullable|array',
                    'default_options.*' => 'nullable|string',
                    'new_options' => 'nullable|array',
                    'new_options.*' => 'nullable|string',
                    'new_default_options' => 'nullable|array',
                    'new_default_options.*' => 'nullable',
                ];
            }

            $validated = $request->validate($validationRules);

            DB::beginTransaction();

            // Generate and validate slug
            $slug = $request->slug ?: Str::slug($request->name);
            $slugExists = Filter::where('category_id', $request->category_id)
                ->where('slug', $slug)
                ->where('id', '!=', $id)
                ->exists();

            if ($slugExists) {
                $originalSlug = $slug;
                $counter = 1;
                do {
                    $slug = $originalSlug . '-' . $counter++;
                } while (
                    Filter::where('category_id', $request->category_id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $id)
                    ->exists()
                );
            }

            // Update filter basic info
            $filter->update([
                'name' => $request->name,
                'slug' => $slug,
                'is_required' => $request->is_required ? 1 : 0,
                'category_id' => $request->category_id,
                'filter_type_id' => $request->filter_type_id,
            ]);

            // Update filter translation
            $translation = FilterTranslation::firstOrNew([
                'filter_id' => $id,
                'language_id' => $lang_id,
            ]);
            $translation->name = $request->name;
            $translation->slug = $slug;
            $translation->save();

            // Check if filter type has changed
            $filterTypeChanged = $originalFilterTypeId != $request->filter_type_id;

            // If filter type changed, remove all existing options
            if ($filterTypeChanged) {
                FilterOption::where('filter_id', $id)->delete();
            }

            // --- TYPE-SPECIFIC LOGIC ---
            if ($filterTypeSlug === 'slider' || $filterTypeSlug === 'toggle') {
                // Find or create new option for slider/toggle type
                $option = FilterOption::firstOrNew([
                    'filter_id' => $id,
                    'filter_type_id' => $filter->filter_type_id,
                ]);

                if ($filterTypeSlug === 'slider') {
                    $option->min_value = $request->min_value;
                    $option->max_value = $request->max_value;
                    $option->unit = $request->unit ?? '';
                    $option->default_range = $request->default_range;
                    // Clear toggle fields if type changed
                    $option->on_label = null;
                    $option->off_label = null;
                    $option->default_toggle = null;
                    $option->name = null;
                } else { // toggle
                    $option->on_label = $request->on_label ?? 'Yes';
                    $option->off_label = $request->off_label ?? 'No';
                    $option->default_toggle = $request->default_toggle ? 1 : 0;
                    // Clear slider fields if type changed
                    $option->min_value = null;
                    $option->max_value = null;
                    $option->unit = null;
                    $option->default_range = null;
                    $option->name = null;
                }

                $option->save();

                // Update option translation
                $optionTrans = FilterOptionTranslation::firstOrNew([
                    'filter_option_id' => $option->id,
                    'language_id' => $lang_id,
                ]);

                if ($filterTypeSlug === 'slider') {
                    $optionTrans->min_value = $request->min_value;
                    $optionTrans->max_value = $request->max_value;
                    $optionTrans->unit = $request->unit ?? '';
                    $optionTrans->default_range = $request->default_range;
                    $optionTrans->on_label = null;
                    $optionTrans->off_label = null;
                    $optionTrans->default_toggle = null;
                    $optionTrans->name = null;
                } else {
                    $optionTrans->on_label = $request->on_label ?? 'Yes';
                    $optionTrans->off_label = $request->off_label ?? 'No';
                    $optionTrans->default_toggle = $request->default_toggle ? 1 : 0;
                    $optionTrans->min_value = null;
                    $optionTrans->max_value = null;
                    $optionTrans->unit = null;
                    $optionTrans->default_range = null;
                    $optionTrans->name = null;
                }

                $optionTrans->save();
            } elseif (in_array($filterTypeSlug, ['dropdown', 'checkbox', 'radio'])) {
                $submittedOptionIds = [];

                // Update existing options
                if ($request->has('options')) {
                    foreach ($request->options as $optionId => $optionName) {
                        if (empty($optionName)) continue;

                        $option = FilterOption::find($optionId);
                        if (!$option) continue;

                        $option->name = $optionName;
                        $option->is_default = isset($request->default_options[$optionId]) ? 1 : 0;
                        // Clear slider/toggle fields if type changed
                        $option->min_value = null;
                        $option->max_value = null;
                        $option->unit = null;
                        $option->default_range = null;
                        $option->on_label = null;
                        $option->off_label = null;
                        $option->default_toggle = null;
                        $option->save();

                        $optionTrans = FilterOptionTranslation::firstOrNew([
                            'filter_option_id' => $option->id,
                            'language_id' => $lang_id,
                        ]);
                        $optionTrans->name = $optionName;
                        // Clear slider/toggle fields in translation
                        $optionTrans->min_value = null;
                        $optionTrans->max_value = null;
                        $optionTrans->unit = null;
                        $optionTrans->default_range = null;
                        $optionTrans->on_label = null;
                        $optionTrans->off_label = null;
                        $optionTrans->default_toggle = null;
                        $optionTrans->save();

                        $submittedOptionIds[] = $optionId;
                    }
                }

                // Add new options
                if ($request->has('new_options')) {
                    foreach ($request->new_options as $index => $optionName) {
                        if (empty($optionName)) continue;

                        $newOption = FilterOption::create([
                            'filter_id' => $id,
                            'filter_type_id' => $filter->filter_type_id,
                            'name' => $optionName,
                            'is_default' => isset($request->new_default_options[$index]) ? 1 : 0,
                        ]);

                        FilterOptionTranslation::create([
                            'filter_option_id' => $newOption->id,
                            'language_id' => $lang_id,
                            'name' => $optionName,
                        ]);

                        $submittedOptionIds[] = $newOption->id;
                    }
                }

                // Delete options not in submission
                FilterOption::where('filter_id', $id)
                    ->whereNotIn('id', $submittedOptionIds)
                    ->delete();
            }

            DB::commit();
            return redirect()->route('filters')->with('success', 'Filter updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Filter update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'An error occurred while updating the filter: ' . $e->getMessage())->withInput();
        }
    }

    public function remove(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $filter = Filter::findOrFail($id);
            $categoryId = $filter->category_id;

            // Delete filter options and their translations
            foreach ($filter->options as $option) {
                FilterOptionTranslation::where('filter_option_id', $option->id)->delete();
                $option->delete();
            }

            // Delete filter translations
            FilterTranslation::where('filter_id', $id)->delete();

            // Delete the filter
            $filter->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Filter and its translations removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
