<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\Log;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TopRatedProduct extends Component
{
    use WithPagination;

    public $lang_id;
    public $ratingCounts = [];
    public $filters = [];
    public $selectedRatings = [];
    public $selectedOptions = [];
    public $searchTerm = '';
    public $minPrice = 0;
    public $maxPrice = 10000;
    public $dynamicMaxPrice = 10000;
    public $isPriceFilterActive = false;
    public $activeFilters = [];
    public $productsCount = 0;
    public $noMatchingProducts = false;
    public $maxPriceValue = 10000;
    public $perPage = 4;
    public $page = 1;
    // Configure URL parameters to match CategoryPage
    protected $queryString = [
        'selectedOptions' => ['except' => []],
        'searchTerm' => ['except' => ''],
        'selectedRatings' => ['except' => []],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 10000],
        'page' => ['except' => 1],
    ];
    public function mount()
    {
        $this->lang_id = getCurrentLanguageID();

        // Load filters using all products
        $allProducts = Product::with([
            'filterOptions.filterOption.filter.translations' => fn($q) => $q->where('language_id', $this->lang_id),
            'filterOptions.filterOption.translations' => fn($q) => $q->where('language_id', $this->lang_id),
            'filterOptions.filterOption.filter.filterType',
        ])
            ->where('lang_id', $this->lang_id)
            ->whereHas('translations', function ($q) {
                $q->where('lang_id', $this->lang_id);
            })
            ->get();

        $this->filters = $this->buildFilters($allProducts);

        // Set initial price range based on database values
        $this->initializePriceRange();

        // Calculate rating counts
        $this->calculateRatingCounts();

        // Initialize active filters structure
        $this->initializeActiveFilters();

        // Load default filter options
        $this->loadDefaultFilterOptions();

        // Update active filters if there are URL parameters or default options
        if (!empty($this->selectedOptions)) {
            $this->updateActiveFilters();
        }

        // Dispatch price range event only once during mount
        $this->dispatch('set-price-range', [
            'maxPrice' => $this->maxPriceValue
        ]);
    }

    /**
     * Load default filter options and add them to selectedOptions
     */
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
    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
            $this->setPage($this->page); // Add this line
            $this->dispatch('scroll-to-middle');
        }
    }

    public function nextPage()
    {
        $totalPages = ceil($this->productsCount / $this->perPage);
        if ($this->page < $totalPages) {
            $this->page++;
            $this->setPage($this->page); // Add this line
            $this->dispatch('scroll-to-middle');
        }
    }

    public function gotoPage($page)
    {
        $this->page = $page;
        $this->setPage($this->page); // Add this line
        $this->dispatch('scroll-to-middle');
    }

    private function resetPage()
    {
        $this->dispatch('scroll-to-middle');
    }
    protected function initializeActiveFilters()
    {
        // Initialize activeFilters with filter names (similar to CategoryPage)
        foreach ($this->filters as $filter) {
            $filterName = $filter->translations->first() ? $filter->translations->first()->name : $filter->name;
            $filterType = $filter->filterType ? $filter->filterType->slug : 'checkbox';

            $this->activeFilters[$filter->id] = [
                'name' => $filterName,
                'type' => $filterType,
                'options' => [],
                'display_order' => $filter->display_order ?? 1 // Fallback order if not set
            ];
        }
        // Sort activeFilters by display_order
        uasort($this->activeFilters, function ($a, $b) {
            return ($a['display_order'] ?? 1) <=> ($b['display_order'] ?? 1);
        });
    }

    // public function calculateRatingCounts()
    // {
    //     // Initialize counts for all ratings
    //     $this->ratingCounts = [
    //         5 => 0,
    //         4 => 0,
    //         3 => 0,
    //         2 => 0,
    //         1 => 0
    //     ];

    //     // Get all businesses with their reviews
    //     $businesses = Business::with([
    //         'reviews' => function ($q) {
    //             $q->where('status', 'active');
    //         }
    //     ])
    //         ->whereHas('languages', function ($query) {
    //             $query->where('language_id', $this->lang_id);
    //         })
    //         ->where(function ($query) {
    //             $query->where('active_all_countries', 1)
    //                 ->orWhere(function ($q) {
    //                     $q->where('active_all_countries', 0)
    //                         ->whereHas('countries', function ($countryQuery) {
    //                             $countryQuery->where('country_id', getCurrentCountry());
    //                         });
    //                 });
    //         })
    //         ->withAvg(['reviews as avg_rating' => function ($q) {
    //             $q->where('status', 'active');
    //         }], 'rating')
    //         ->orderByDesc('avg_rating')
    //         ->get();


    //     // Count businesses for each rating level
    //     foreach ($businesses as $business) {
    //         $avgRating = $business->reviews->avg('rating');
    //         if ($avgRating) {
    //             foreach ([5, 4, 3, 2, 1] as $rating) {
    //                 if ($avgRating >= $rating) {
    //                     $this->ratingCounts[$rating]++;
    //                 }
    //             }
    //         }
    //     }
    // }

    public function calculateRatingCounts()
    {
        // Reset counts
        $this->ratingCounts = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        ];

        // Use the same filtered businesses that are returned by getProductsProperty()
        $businesses = $this->getProductsProperty()->getCollection(); // Get raw collection from paginator

        foreach ($businesses as $business) {
            $avgRating = $business->reviews->avg('rating');

            if ($avgRating) {
                foreach ([5, 4, 3, 2, 1] as $rating) {
                    if ($avgRating >= $rating) {
                        $this->ratingCounts[$rating]++;
                    }
                }
            }
        }
    }


    protected function initializePriceRange()
    {
        // Get min and max prices from database to set slider boundaries
        $priceStats = \App\Models\ProductPrice::selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        if ($priceStats) {
            // Set the dynamic maximum price (ensure it's a number and not zero)
            $this->maxPriceValue = max(ceil($priceStats->max_price), 100);

            // Allow URL parameters to override defaults
            $this->minPrice = request()->has('minPrice') ? (int)request('minPrice') : floor($priceStats->min_price);
            $this->maxPrice = request()->has('maxPrice') ? (int)request('maxPrice') : $this->maxPriceValue;
        }
    }

    protected function buildFilters($products)
    {
        $filters = collect();

        foreach ($products as $product) {
            foreach ($product->filterOptions as $productFilterOption) {
                $option = $productFilterOption->filterOption;
                if (!$option || !$option->filter) {
                    continue;
                }

                $filter = $option->filter;

                if (!$filters->has($filter->id)) {
                    $filter->loadMissing([
                        'translations' => fn($q) => $q->where('language_id', $this->lang_id),
                        'options.translations' => fn($q) => $q->where('language_id', $this->lang_id),
                        'filterType',
                    ]);
                    $filters->put($filter->id, $filter);
                }
            }
        }

        return $filters->sortBy('display_order')->values();
    }

    public function getProductsProperty()
    {
        // Start with businesses query
        $query = Business::whereHas('translations', function ($q) {
            $q->where('lang_id', $this->lang_id);
        })->whereHas('languages', function ($query) {
            $query->where('language_id', $this->lang_id);
        })
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                    ->orWhere(function ($q) {
                        $q->where('active_all_countries', 0)
                            ->whereHas('countries', function ($countryQuery) {
                                $countryQuery->where('country_id', getCurrentCountry());
                            });
                    });
            })
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'products' => fn($q) => $q
                    ->with(['prices' => fn($p) => $p->orderBy('price')])
                    ->where(function ($query) {
                        $query->where('active_all_countries', 1)
                            ->orWhere(function ($q) {
                                $q->where('active_all_countries', 0)
                                    ->whereHas('countries', function ($countryQuery) {
                                        $countryQuery->where('country_id', getCurrentCountry());
                                    });
                            });
                    }),
                'limitedFeatures.translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'reviews' => fn($q) => $q->with('translations')
                    ->whereHas('translations', fn($q) => $q->where('language_id', $this->lang_id))
                    ->where('status', 'active')
            ])
            ->withAvg(['reviews as avg_rating' => function ($q) {
                $q->where('status', 'active');
            }], 'rating')
            ->orderByDesc('avg_rating') // Order by rating high to low
            ->orderBy('id'); // Secondary sort for consistent pagination
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

        $businesses->each(function ($business) {
            if ($business->icon_id && !file_exists(public_path($business->icon_id))) {
                \Log::warning("Business icon missing: {$business->icon_id} for business ID: {$business->id}");
                $business->icon_id = null;
            }
        });

        // Filter businesses by price range
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
            })->filter(fn($p) => !is_null($p));

            if ($validPrices->isEmpty()) return false;

            $min = $validPrices->min();
            return $min >= $this->minPrice && $min <= $this->maxPrice;
        });

        $filtered = $filtered->sortByDesc(function ($business) {
            return $business->avg_rating ?? 0; // Use 0 for businesses without ratings
        })->values(); // Reset array keys

        // Count results
        $totalCount = $filtered->count();
        $this->productsCount = $totalCount;
        $this->noMatchingProducts = $totalCount === 0;

        $currentPage = max(1, $this->page);
        $maxPage = max(1, ceil($totalCount / $this->perPage));

        if ($currentPage > $maxPage) {
            $currentPage = $maxPage;
            $this->setPage($currentPage);
        }

        $paginated = $filtered->forPage($currentPage, $this->perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginated,
            $totalCount,
            $this->perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
                'query' => request()->query()
            ]
        );

        return $paginator;
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
                // For radio buttons, unselect other options from the same filter
                $this->selectedOptions = array_filter($this->selectedOptions, function ($id) use ($filterId) {
                    $filter = $this->filters->firstWhere('id', $filterId);
                    if (!$filter) return true;

                    foreach ($filter->options as $option) {
                        if ($option->id == $id) {
                            return false; // Remove if this option belongs to the same filter
                        }
                    }
                    return true;
                });
                // Add the selected option
                $this->selectedOptions[] = $optionId;
                break;

            case 'dropdown':
                // First, remove any existing selection for this filter
                // Remove existing selections for this filter only
                $this->selectedOptions = array_filter($this->selectedOptions, function ($id) use ($filterId) {
                    $filter = $this->filters->firstWhere('id', $filterId);
                    if (!$filter) return true;

                    foreach ($filter->options as $option) {
                        if ($option->id == $id) {
                            return false; // Remove if this option belongs to the same filter
                        }
                    }
                    return true;
                });
                // Then add the new selection
                $this->selectedOptions[$filterId] = $optionId;
                break;

            case 'toggle':
                // Toggle works like checkbox
            case 'color':
                // Color selection works like checkbox too
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

    public function hasActiveFilters()
    {
        return !empty($this->selectedOptions)
            || !empty($this->searchTerm)
            || !empty($this->selectedRatings)
            || $this->isPriceFilterActive;
    }

    public function removeFilter($optionId)
    {
        $this->selectedOptions = array_diff($this->selectedOptions, [$optionId]);
        $this->updateActiveFilters();
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->selectedOptions = [];
        $this->searchTerm = '';
        $this->selectedRatings = [];
        $this->isPriceFilterActive = false;
        $this->initializePriceRange();

        // Reset active filters
        foreach ($this->activeFilters as $filterId => $data) {
            $this->activeFilters[$filterId]['options'] = [];
        }

        // Re-load default filter options
        $this->loadDefaultFilterOptions();

        // Update active filters with defaults
        $this->updateActiveFilters();

        $this->resetPage();
        $this->dispatch('scroll-to-middle');
        return redirect()->route('top-rated-product', [
            'locale' => getCurrentLocale(), // or app()->getLocale()
        ]);
        
    }
    public function render()
    {
        return view('livewire.top-rated-product', [
            'products' => $this->products,
            'filters' => $this->filters,
        ]);
    }
}
