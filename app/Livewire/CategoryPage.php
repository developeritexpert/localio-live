<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\Category;
use App\Models\Filter;
use App\Models\FilterType;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryPage extends Component
{
    use WithPagination;

    // Basic properties
    public $category;
    public $slug;
    public $lang_id = 1;
    public $searchTerm = '';
    public $country_id=1;
    
    // Parent Category properties
    public $isParentCategory = false;
    public $parentSubCategories = [];

    // Filter properties
    public $minPrice = 0;
    public $maxPrice = 2000;
    public $selectedOptions = [];
    public $selectedRatings = [];
    public $filters = [];
    public $filterTypes = [];
    public $activeFilters = [];
    public $isPriceFilterActive = false;
    public $ratingCounts;

    // Results properties
    public $productsCount = 0;
    public $noMatchingProducts = false;

    // Configure URL parameters
    protected $queryString = [
        'selectedOptions' => ['except' => []],
        'searchTerm' => ['except' => ''],
        'selectedRatings' => ['except' => []],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 2000],
    ];

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->lang_id = getCurrentLanguageID();
        $this->country_id= getCurrentCountry();
        // Get category data
        $this->category = Category::whereHas('translations', function ($query) {
            $query->where('slug', $this->slug)
                ->where('lang_id', $this->lang_id);
        })->with(['translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->firstOrFail();

        // Load filter types
        $this->filterTypes = FilterType::pluck('slug', 'id')->toArray();

        // Initialize price range from database
        $this->initializePriceRange();

        // Load filters and calculate ratings
        $this->loadFilters();
        $this->calculateRatingCounts();
        // Load default filter options
        $this->loadDefaultFilterOptions();
        // Update active filters if there are URL parameters
        if (!empty($this->selectedOptions)) {
            $this->updateActiveFilters();
        }

        // Check if this is a parent category
        if (is_null($this->category->parent_id)) {
            $this->loadParentCategoryData();
            if ($this->parentSubCategories->isNotEmpty()) {
                $this->isParentCategory = true;
            }
        }
    }

    protected function loadParentCategoryData()
    {
        // Load subcategories with their top products
        $this->parentSubCategories = Category::where('parent_id', $this->category->id)
            ->with(['translations' => function ($query) {
                $query->where('lang_id', $this->lang_id);
            }])
            ->get()
            ->map(function ($subcat) {
                // Fetch top 6 businesses for this subcategory
                $businesses = Business::where('category_id', $subcat->id)
                    ->where('status', 1)
                    ->whereHas('languages', function ($query) {
                        $query->where('language_id', $this->lang_id);
                    })
                    ->where(function ($query) {
                        $query->where('active_all_countries', 1)
                            ->orWhereHas('countries', function ($q) {
                                $q->where('country_id', $this->country_id);
                            });
                    })
                    ->with(['translations' => function ($q) {
                        $q->where('lang_id', $this->lang_id);
                    }])
                    ->withCount(['reviews as active_reviews_count' => function ($query) {
                        $query->where('status', 'active');
                    }])
                    ->withAvg(['reviews as average_rating' => function ($query) {
                        $query->where('status', 'active');
                    }], 'rating')
                    ->orderBy('average_rating', 'desc')
                    ->take(6)
                    ->get();
                
                $subcat->top_businesses = $businesses;
                return $subcat;
            });
    }

    protected function initializePriceRange()
    {
        // Get min and max prices from database to set slider boundaries
        $priceStats = \App\Models\ProductPrice::selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        if ($priceStats) {
            // Set initial values and boundaries
            $this->minPrice = request()->has('minPrice') ? (int)request('minPrice') : floor($priceStats->min_price);
            $this->maxPrice = request()->has('maxPrice') ? (int)request('maxPrice') : ceil($priceStats->max_price);
        }
    }
    protected function loadDefaultFilterOptions()
    {
        // Don't set defaults if URL parameters already exist
        if (!empty($this->selectedOptions)) {
            return;
        }

        foreach ($this->filters as $filter) {
            // Get filter type
            $filterType = $filter->filterType ? $filter->filterType->slug : 'checkbox';

            // Find default options
            $defaultOptions = $filter->options->where('is_default', true);

            if ($defaultOptions->isNotEmpty()) {
                // For radio and dropdown, only select the first default option
                if (in_array($filterType, ['radio', 'dropdown']) && $defaultOptions->count() > 0) {
                    $this->selectedOptions[] = $defaultOptions->first()->id;
                } else {
                    // For checkbox, toggle, color - select all default options
                    foreach ($defaultOptions as $option) {
                        $this->selectedOptions[] = $option->id;
                    }
                }
            }
        }
    }
    public function loadFilters()
    {
        // Load filters by category with appropriate relationships
        $this->filters = Filter::where('category_id', $this->category->id)
            ->where('status', 'active')
            ->with([
                'filterType',
                'translations' => function ($query) {
                    $query->where('language_id', $this->lang_id);
                },
                'options', // Removed the problematic orderBy
                'options.translations' => function ($query) {
                    $query->where('language_id', $this->lang_id);
                },
                'options.filterType'
            ])
            ->orderBy('display_order')
            ->get();

        // Initialize activeFilters with filter names
        foreach ($this->filters as $filter) {
            $filterName = $filter->translations->first() ? $filter->translations->first()->name : $filter->name;
            $filterType = $filter->filterType ? $filter->filterType->slug : 'checkbox';

            $this->activeFilters[$filter->id] = [
                'name' => $filterName,
                'type' => $filterType,
                'options' => []
            ];
        }
    }
    public function updated()
    {
        $this->calculateRatingCounts();
    }
    public function calculateRatingCounts()
    {
        $businesses = Business::select('id')
            ->where('category_id', $this->category->id)
            ->where('status', 1)
            ->withAvg(['reviews' => function ($query) {
                $query->where('status', 'active');
            }], 'rating')
            ->whereHas('languages', function ($query) {
                $query->where('language_id', $this->lang_id);
            })
            ->whereHas('countries', function ($query) {
                $query->where('country_id', $this->country_id);
            })
            ->get();

        $counts = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        foreach ($businesses as $business) {
            $avg = $business->reviews_avg_rating;

            foreach ([5, 4, 3, 2, 1] as $threshold) {
                if ($avg >= $threshold) {
                    $counts[$threshold]++;
                }
            }
        }

        $this->ratingCounts = $counts;
    }


    public function getProductsProperty()
    {
        // Start with businesses in this category
        $query = Business::where('category_id', $this->category->id)
            ->where('status', 1)
            ->with([
                'translations' => function ($q) {
                    $q->where('lang_id', $this->lang_id);
                },'limitedFeatures.translations'=>fn($q) => $q->where('lang_id', $this->lang_id),
                'products.prices' => fn($q) => $q->orderBy('price'),
                'reviews' => fn($q) => $q->with('translations')
                    ->whereHas('translations', fn($q) => $q->where('language_id', $this->lang_id))
                    ->where('status', 'active')
            ])
            ->whereHas('languages', function ($query) {
                $query->where('language_id', $this->lang_id);
            })
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                    ->orWhere(function ($q) {
                        $q->where('active_all_countries', 0)
                            ->whereHas('countries', function ($countryQuery) {
                                $countryQuery->where('country_id', $this->country_id);
                            });
                    });
            });
        // Apply search filter if exists
        if (!empty($this->searchTerm)) {
            $query->whereHas('translations', function ($q) {
                $q->where('lang_id', $this->lang_id)
                    ->where(function ($sq) {
                        $sq->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        // Apply rating filter if selected
        if (!empty($this->selectedRatings)) {
            $query->whereHas('reviews', function ($q) {
                $q->select('business_id')
                    ->where('status', 'active')
                    ->groupBy('business_id')
                    ->havingRaw('AVG(rating) >= ?', [min($this->selectedRatings)]);
            });
        }
        // Group selected options by filter ID for more accurate filtering
        $groupedOptions = [];
        foreach ($this->selectedOptions as $optionId) {
            foreach ($this->filters as $filter) {
                foreach ($filter->options as $option) {
                    if ($option->id == $optionId) {
                        $filterId = $filter->id;
                        if (!isset($groupedOptions[$filterId])) {
                            $groupedOptions[$filterId] = [
                                'type' => $filter->filterType ? $filter->filterType->slug : 'checkbox',
                                'options' => []
                            ];
                        }
                        $groupedOptions[$filterId]['options'][] = $optionId;
                        break 2;
                    }
                }
            }
        }

        // Apply filter options - using subqueries for proper filtering of business products
        foreach ($groupedOptions as $filterId => $filterData) {
            $query->whereHas('products', function ($productQuery) use ($filterId, $filterData) {
                $productQuery->whereHas('filterOptions', function ($optionQuery) use ($filterId, $filterData) {
                    $optionQuery->where('filter_id', $filterId)
                        ->whereIn('filter_option_id', $filterData['options']);
                });
            });
        }


        $businesses = $query->get();

        $filtered = $businesses->filter(function ($business) {
            $validPrices = $business->products->flatMap(function ($product) {
                return $product->prices;
            })->map(function ($price) {
                $now = now();
                if ($price->discount_price && $price->discount_expiration_date && $now->lte($price->discount_expiration_date)) {
                    return $price->discount_price;
                } elseif ($price->renewal_price) {
                    return $price->renewal_price;
                } else {
                    return $price->price;
                }
            })->filter(fn($p) => !is_null($p)); // Remove nulls

            if ($validPrices->isEmpty()) return false;

            $min = $validPrices->min();

            return $min >= $this->minPrice && $min <= $this->maxPrice;
        });

        // Count before pagination
        $totalCount = $filtered->count();
        $this->productsCount = $totalCount;
        $this->noMatchingProducts = $totalCount === 0;

        // Paginate manually using Laravel collection
        $page = request()->get('page', 1);
        $perPage = 10;
        $paginated = $filtered->forPage($page, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginated,
            $totalCount,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function updateActiveFilters()
    {
        // Reset active filter options
        foreach ($this->activeFilters as $filterId => $data) {
            $this->activeFilters[$filterId]['options'] = [];
        }

        // Update selected options in active filters
        foreach ($this->selectedOptions as $optionId) {
            foreach ($this->filters as $filter) {
                foreach ($filter->options as $option) {
                    if ($option->id == $optionId) {
                        $optionName = $option->translations->first() ?
                            $option->translations->first()->name : $option->name;

                        $this->activeFilters[$filter->id]['options'][$optionId] = $optionName;
                        break 2;
                    }
                }
            }
        }

    }


    protected function getListeners()
    {
        return [
            'set-price-range' => 'setPriceRange',
        ];
    }

    // Method to handle price range updates from slider
    public function setPriceRange($min, $max)
    {
        // Ensure we have valid numbers
        $min = is_numeric($min) ? (int)$min : 0;
        $max = is_numeric($max) ? (int)$max : 2000;

        // Apply validations
        $this->minPrice = $min;
        $this->maxPrice = $max;
        $this->isPriceFilterActive = true;
        $this->resetPage();
        $this->dispatch('scroll-to-middle');

    }

    // Livewire lifecycle hooks for updates
    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->dispatch('scroll-to-middle');

    }

    public function updatedSelectedOptions()
    {
        $this->updateActiveFilters();
        $this->resetPage();
        $this->dispatch('scroll-to-middle');
    }

    public function updatedSelectedRatings()
    {
        $this->resetPage();
        $this->dispatch('scroll-to-middle');

    }

    public function updatedMinPrice()
    {
        $this->isPriceFilterActive = true;

        // Make sure minPrice doesn't exceed maxPrice
        if ($this->minPrice > $this->maxPrice) {
            $this->minPrice = $this->maxPrice;
        }

        $this->resetPage();
        $this->dispatch('scroll-to-middle');

    }

    public function updatedMaxPrice()
    {
        $this->isPriceFilterActive = true;

        // Make sure maxPrice is not less than minPrice
        if ($this->maxPrice < $this->minPrice) {
            $this->maxPrice = $this->minPrice;
        }

        $this->resetPage();
        $this->dispatch('scroll-to-middle');

    }

    // Filter operations
    public function toggleFilterOption($optionId)
{
    // Find option and get filter type
    $filterType = null;
    $filterId = null;

    foreach ($this->filters as $filter) {
        foreach ($filter->options as $option) {
            if ($option->id == $optionId) {
                $filterType = $filter->filterType ? $filter->filterType->slug : 'checkbox';
                $filterId = $filter->id;
                break 2;
            }
        }
    }
    // Handle based on filter type
    switch ($filterType) {
        case 'radio':
            // For radio buttons, first remove ALL options from this specific filter
            $currentFilter = $this->filters->find($filterId);
            if ($currentFilter) {
                $filterOptionIds = $currentFilter->options->pluck('id')->toArray();
                $this->selectedOptions = array_diff($this->selectedOptions, $filterOptionIds);
            }
            // Then add the newly selected option
            $this->selectedOptions[] = $optionId;
            break;
        case 'dropdown':
            // For dropdown, replace existing selection for this filter with the new one
            $currentFilter = $this->filters->find($filterId);
            if ($currentFilter) {
                $filterOptionIds = $currentFilter->options->pluck('id')->toArray();
                $this->selectedOptions = array_diff($this->selectedOptions, $filterOptionIds);
            }
            $this->selectedOptions[] = $optionId;
            break;

        case 'toggle':
        case 'checkbox':
        default:
            // For checkbox, toggle the selection
            if (in_array($optionId, $this->selectedOptions)) {
                $this->selectedOptions = array_diff($this->selectedOptions, [$optionId]);
            } else {
                $this->selectedOptions[] = $optionId;
            }
            break;
    }

    // Update active filters display
    $this->updateActiveFilters();
    $this->resetPage();
    $this->dispatch('scroll-to-middle');
}

    public function removeFilter($optionId)
    {
        $this->selectedOptions = array_diff($this->selectedOptions, [$optionId]);
        $this->updateActiveFilters();
        $this->resetPage();
        $this->dispatch('scroll-to-middle');

    }
    public function resetFilters()
    {
        $this->selectedOptions = [];
        $this->searchTerm = '';
        $this->selectedRatings = [];
        $this->isPriceFilterActive = false;
        $this->initializePriceRange();
        $this->loadDefaultFilterOptions();
       
        // Reset active filters
        // dd($this->activeFilters);
        foreach ($this->activeFilters as $filterId => $data) {
            $this->activeFilters[$filterId]['options'] = [];
        }

        $this->resetPage();
        $this->dispatch('scroll-to-middle');
        return redirect()->route('category.detail', [
            'locale' => getCurrentLocale(), // or app()->getLocale()
            'slug' => $this->slug,
        ]);
        
    }

    public function render()
    {
        return view('livewire.category-page', [
            'products' => $this->products
        ]);
    }
}
