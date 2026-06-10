<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Business;
use App\Models\Log;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class ExclusiveBusinesses extends Component
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

        // Build filters from products that actually have offers (same as getProductsProperty logic)
        $this->buildFiltersFromOfferedProducts();

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
    protected function buildFiltersFromOfferedProducts()
    {
        // Get businesses with products that have valid discounts (same logic as getProductsProperty)
        $businesses = Business::whereHas('translations', function ($q) {
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
        // Only fetch businesses that have products with valid discounts
        ->whereHas('products', function ($productQuery) {
            $productQuery->where(function ($query) {
                $query->where('active_all_countries', 1)
                    ->orWhere(function ($q) {
                        $q->where('active_all_countries', 0)
                            ->whereHas('countries', function ($countryQuery) {
                                $countryQuery->where('country_id', getCurrentCountry());
                            });
                    });
            })
            // Filter products that have valid discount prices
            ->whereHas('prices', function ($priceQuery) {
                $priceQuery->whereNotNull('discount_price')
                    ->where('discount_price', '>', 0)
                    ->whereNotNull('discount_expiration_date')
                    ->where('discount_expiration_date', '>=', now());
            });
        })
        ->with([
            'products' => fn($q) => $q
                ->with([
                    'filterOptions.filterOption.filter.translations' => fn($q) => $q->where('language_id', $this->lang_id),
                    'filterOptions.filterOption.translations' => fn($q) => $q->where('language_id', $this->lang_id),
                    'filterOptions.filterOption.filter.filterType',
                ])
                ->where('lang_id', $this->lang_id)
                ->whereHas('translations', function ($q) {
                    $q->where('lang_id', $this->lang_id);
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
                // Only include products that have valid discount prices
                ->whereHas('prices', function ($priceQuery) {
                    $priceQuery->whereNotNull('discount_price')
                        ->where('discount_price', '>', 0)
                        ->whereNotNull('discount_expiration_date')
                        ->where('discount_expiration_date', '>=', now());
                })
        ])
        ->get();

        // Extract all products from businesses
        $offeredProducts = collect();
        foreach ($businesses as $business) {
            foreach ($business->products as $product) {
                $offeredProducts->push($product);
            }
        }

        // Build filters from these products only
        $this->filters = $this->buildFilters($offeredProducts);
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

        // Get only filtered businesses
        $businesses = $this->getProductsProperty()->getCollection(); // get raw Collection from paginator

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
            // Only fetch businesses that have products with valid discounts
            ->whereHas('products', function ($productQuery) {
                $productQuery->where(function ($query) {
                    $query->where('active_all_countries', 1)
                        ->orWhere(function ($q) {
                            $q->where('active_all_countries', 0)
                                ->whereHas('countries', function ($countryQuery) {
                                    $countryQuery->where('country_id', getCurrentCountry());
                                });
                        });
                })
                    // Filter products that have valid discount prices
                    ->whereHas('prices', function ($priceQuery) {
                        $priceQuery->whereNotNull('discount_price')
                            ->where('discount_price', '>', 0)
                            ->whereNotNull('discount_expiration_date')
                            ->where('discount_expiration_date', '>=', now());
                    });
            })
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'products' => fn($q) => $q
                    ->with([
                        // Only load prices that have valid discounts
                        'prices' => fn($p) => $p->where(function ($priceQuery) {
                            $priceQuery->whereNotNull('discount_price')
                                ->where('discount_price', '>', 0)
                                ->whereNotNull('discount_expiration_date')
                                ->where('discount_expiration_date', '>=', now());
                        })->orderBy('discount_price'), // Order by discount price instead
                        'translations' => fn($t) => $t->where('lang_id', $this->lang_id)
                    ])
                    ->where(function ($query) {
                        $query->where('active_all_countries', 1)
                            ->orWhere(function ($q) {
                                $q->where('active_all_countries', 0)
                                    ->whereHas('countries', function ($countryQuery) {
                                        $countryQuery->where('country_id', getCurrentCountry());
                                    });
                            });
                    })
                    // Only include products that have valid discount prices
                    ->whereHas('prices', function ($priceQuery) {
                        $priceQuery->whereNotNull('discount_price')
                            ->where('discount_price', '>', 0)
                            ->whereNotNull('discount_expiration_date')
                            ->where('discount_expiration_date', '>=', now());
                    }),
                'limitedFeatures.translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'reviews' => fn($q) => $q->with('translations')
                    ->whereHas('translations', fn($q) => $q->where('language_id', $this->lang_id))
                    ->where('status', 'active')
            ]);

        // Apply search filter if exists
        if (!empty($this->searchTerm)) {
            // dd('here');
            $query->whereHas('translations', function ($q) {
                $q->where('lang_id', $this->lang_id)
                    ->where(function ($sq) {
                        $sq->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
            // dd($query->first());
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

        // Handle missing icons
        $businesses->each(function ($business) {
            if ($business->icon_id && !file_exists(public_path($business->icon_id))) {
                Log::warning("Business icon missing: {$business->icon_id} for business ID: {$business->id}");
                $business->icon_id = null;
            }
        });

        // Process businesses and keep only the best deal product per business
        $businesses->each(function ($business) {
            $bestDeal = null;
            $bestDiscountPrice = null;
            $bestDiscountPercentage = 0;

            // Find the product with the best deal (lowest discount price or highest discount percentage)
            foreach ($business->products as $product) {
                $bestPrice = $product->prices->sortBy('discount_price')->first();

                if ($bestPrice && $bestPrice->discount_price && $bestPrice->price > 0) {
                    $discountPercentage = round((($bestPrice->price - $bestPrice->discount_price) / $bestPrice->price) * 100);

                    // Determine if this is the best deal (you can adjust criteria as needed)
                    $isBetterDeal = false;
                    if ($bestDeal === null) {
                        $isBetterDeal = true;
                    } elseif ($bestPrice->discount_price < $bestDiscountPrice) {
                        // Lower price is better
                        $isBetterDeal = true;
                    } elseif ($bestPrice->discount_price == $bestDiscountPrice && $discountPercentage > $bestDiscountPercentage) {
                        // Same price but higher discount percentage
                        $isBetterDeal = true;
                    }

                    if ($isBetterDeal) {
                        $bestDeal = $product;
                        $bestDiscountPrice = $bestPrice->discount_price;
                        $bestDiscountPercentage = $discountPercentage;

                        $product->discount_percentage = $discountPercentage;
                        $product->has_exclusive_deal = true;
                        $product->original_price = $bestPrice->price;
                        $product->discounted_price = $bestPrice->discount_price;
                        $product->currency = $bestPrice->currency;
                    }
                }
            }

            // Replace the products collection with only the best deal product
            if ($bestDeal) {
                $business->setRelation('products', collect([$bestDeal]));
                $business->best_deal_product = $bestDeal;
            } else {
                // If no valid deal found, set empty collection
                $business->setRelation('products', collect([]));
            }
        });

        // Filter businesses by price range (using the best deal discount price)
        $filtered = $businesses->filter(function ($business) {
            // Since we now only have one product per business (the best deal), check its price
            $bestDealProduct = $business->products->first();

            if (!$bestDealProduct) return false;

            $bestPrice = $bestDealProduct->prices->first();
            if (!$bestPrice || !$bestPrice->discount_price) return false;

            return $bestPrice->discount_price >= $this->minPrice && $bestPrice->discount_price <= $this->maxPrice;
        });

        $filtered = $filtered->sortByDesc(function ($business) {
            return $business->avg_rating ?? 0;
        })->values();

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
                'query' => request()->query()
            ]
        );
        return $paginator;
    }
    public function updateActiveFilters()
    {
        if (!is_array($this->activeFilters)) {
            $this->activeFilters = [];
        }

        if (!is_array($this->selectedOptions)) {
            $this->selectedOptions = [];
        }

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

    public function removeFilter($optionId)
    {
        $this->selectedOptions = array_diff($this->selectedOptions, [$optionId]);
        $this->updateActiveFilters();
        $this->resetPage();
    }

    // Renamed from clearFilters to resetFilters to match CategoryPage
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

        return redirect()->route('exclusive-business-deals', [
            'locale' => getCurrentLocale(), // or app()->getLocale()
        ]);
    }

    public function hasActiveFilters()
    {
        return !empty($this->selectedOptions)
            || !empty($this->searchTerm)
            || !empty($this->selectedRatings)
            || $this->isPriceFilterActive;
    }

    public function render()
    {
        return view('livewire.exclusive-businesses', [
            'products' => $this->products,
            'filters' => $this->filters,
        ]);


    }
}


