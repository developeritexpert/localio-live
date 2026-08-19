<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\Category;
use App\Models\Log;
use App\Models\Product;
use App\Models\TopProductContent;
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
    public $categorySlug = null;
    public $sortBy = 'highest_rated';
    public $showSortDropdown = false;
    // Configure URL parameters - page is now path-based, not query string
    protected $queryString = [
        'selectedOptions' => ['except' => []],
        'searchTerm' => ['except' => ''],
        'selectedRatings' => ['except' => []],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 10000],
        'sortBy' => ['except' => 'highest_rated'],
    ];
    public function mount($initialPage = 1, $category = null)
    {
        $this->page = (int) $initialPage;
        $this->categorySlug = $category;
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

        // Set the page in the paginator
        if ($this->page > 1) {
            $this->setPage($this->page);
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
            $this->setPage($this->page);
            $this->dispatchPaginationUrl();
            $this->dispatch('scroll-to-middle');
        }
    }

    public function nextPage()
    {
        $totalPages = ceil($this->productsCount / $this->perPage);
        if ($this->page < $totalPages) {
            $this->page++;
            $this->setPage($this->page);
            $this->dispatchPaginationUrl();
            $this->dispatch('scroll-to-middle');
        }
    }

    public function gotoPage($page)
    {
        $this->page = $page;
        $this->setPage($this->page);
        $this->dispatchPaginationUrl();
        $this->dispatch('scroll-to-middle');
    }

    private function resetPage()
    {
        $this->page = 1;
        $this->dispatchPaginationUrl();
        $this->dispatch('scroll-to-middle');
    }

    public function dispatchPaginationUrl()
    {
        $this->dispatch('update-pagination-url', url: $this->getCleanUrl($this->page));
    }

    public function getCleanUrl($page)
    {
        $locale = app()->getLocale();

        if ($page > 1) {
            if ($this->categorySlug) {
                $url = '/' . $locale . '/top-rated-products/' . $this->categorySlug . '/' . $page;
            } else {
                $url = '/' . $locale . '/top-rated-products/' . $page;
            }
        } else {
            if ($this->categorySlug) {
                $url = '/' . $locale . '/top-rated-products/' . $this->categorySlug;
            } else {
                $url = '/' . $locale . '/top-rated-products';
            }
        }

        // Append filter query params
        $queryParams = [];
        if (!empty($this->selectedRatings)) $queryParams['selectedRatings'] = $this->selectedRatings;
        if (!empty($this->selectedOptions)) $queryParams['selectedOptions'] = $this->selectedOptions;
        if ($this->searchTerm !== '') $queryParams['searchTerm'] = $this->searchTerm;
        if ($this->minPrice != 0) $queryParams['minPrice'] = $this->minPrice;
        if ($this->maxPrice != $this->maxPriceValue) $queryParams['maxPrice'] = $this->maxPrice;

        return empty($queryParams) ? $url : $url . '?' . http_build_query($queryParams);
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

        $maxVal = ($priceStats && $priceStats->max_price) ? max((int)ceil($priceStats->max_price), 100) : 5000;
        $this->maxPriceValue = $maxVal;

        $hasMinParam = request()->has('minPrice') && (int)request('minPrice') > 0;
        $hasMaxParam = request()->has('maxPrice') && (int)request('maxPrice') < $maxVal;

        $this->minPrice = $hasMinParam ? (int)request('minPrice') : 0;
        $this->maxPrice = request()->has('maxPrice') ? (int)request('maxPrice') : $maxVal;

        $this->isPriceFilterActive = ($hasMinParam || $hasMaxParam);
    }

    public function updatePriceFilterState()
    {
        $maxLimit = $this->maxPriceValue ?: 5000;
        $isMinActive = (int)$this->minPrice > 0;
        $isMaxActive = (int)$this->maxPrice < $maxLimit;

        $this->isPriceFilterActive = ($isMinActive || $isMaxActive);
    }

    public function setPriceRange($min = 0, $max = null)
    {
        if (is_array($min)) {
            $max = $min['max'] ?? ($this->maxPriceValue ?: 5000);
            $min = $min['min'] ?? 0;
        }

        $maxLimit = $this->maxPriceValue ?: 5000;
        $this->minPrice = is_numeric($min) ? (int)$min : 0;
        $this->maxPrice = ($max !== null && is_numeric($max)) ? (int)$max : $maxLimit;

        $this->updatePriceFilterState();
        $this->resetPage();
        $this->dispatch('scroll-to-middle');
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

    public function getTopAffiliatedBusinessIdProperty()
    {
        return Business::where('status', 1)
            ->where('is_affiliate', 1)
            ->whereHas('languages', function ($query) {
                $query->where('language_id', $this->lang_id);
            })
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                    ->orWhereHas('countries', function ($countryQuery) {
                        $countryQuery->where('country_id', getCurrentCountry());
                    });
            })
            ->withAvg(['reviews as avg_rating' => function ($q) {
                $q->where('status', 'active');
            }], 'rating')
            ->orderByDesc('avg_rating')
            ->orderBy('id')
            ->value('id');
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
            ->orderByDesc('is_affiliate')
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
            if (!$business->is_affiliate) {
                return !$this->isPriceFilterActive;
            }

            if (!$this->isPriceFilterActive) {
                return true;
            }

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

        $getStartingPrice = function ($business) {
            $validPrices = $business->products->flatMap(function ($product) {
                return $product->prices;
            })->map(function ($price) {
                $now = now();
                if ($price->discount_price && $price->discount_expiration_date && $now->lte($price->discount_expiration_date)) {
                    return (float)$price->discount_price;
                } elseif ($price->renewal_price) {
                    return (float)$price->renewal_price;
                } else {
                    return (float)$price->price;
                }
            })->filter(fn($p) => !is_null($p));

            return $validPrices->isNotEmpty() ? $validPrices->min() : null;
        };

        $getMetrics = function ($business) use ($getStartingPrice) {
            $activeReviews = $business->reviews ? $business->reviews->where('status', 'active') : collect();
            $reviewCount = $activeReviews->count();
            $avgRating = $reviewCount > 0 ? (float)$activeReviews->avg('rating') : 0.0;
            $recCount = $activeReviews->where('recommend', 1)->count();
            $recRate = $reviewCount > 0 ? (float)(($recCount / $reviewCount) * 100) : 0.0;
            $name = mb_strtolower(trim($business->translations->first()?->name ?? $business->name ?? ''));
            $price = $getStartingPrice($business);

            return [
                'is_affiliate' => (int)($business->is_affiliate ?? 0),
                'name' => $name,
                'avg_rating' => $avgRating,
                'review_count' => $reviewCount,
                'rec_rate' => $recRate,
                'price' => $price,
            ];
        };

        $sortBy = $this->sortBy ?? 'highest_rated';

        $filtered = $filtered->sort(function ($a, $b) use ($sortBy, $getMetrics) {
            $mA = $getMetrics($a);
            $mB = $getMetrics($b);

            switch ($sortBy) {
                case 'most_reviewed':
                    if ($mA['is_affiliate'] !== $mB['is_affiliate']) {
                        return $mB['is_affiliate'] <=> $mA['is_affiliate'];
                    }
                    if ($mA['review_count'] !== $mB['review_count']) {
                        return $mB['review_count'] <=> $mA['review_count'];
                    }
                    return $mB['avg_rating'] <=> $mA['avg_rating'];

                case 'most_recommended':
                    if ($mA['is_affiliate'] !== $mB['is_affiliate']) {
                        return $mB['is_affiliate'] <=> $mA['is_affiliate'];
                    }
                    if ($mA['rec_rate'] !== $mB['rec_rate']) {
                        return $mB['rec_rate'] <=> $mA['rec_rate'];
                    }
                    return $mB['avg_rating'] <=> $mA['avg_rating'];

                case 'price_low_high':
                    if ($mA['is_affiliate'] !== $mB['is_affiliate']) {
                        return $mB['is_affiliate'] <=> $mA['is_affiliate'];
                    }
                    $pA = is_null($mA['price']) ? PHP_INT_MAX : $mA['price'];
                    $pB = is_null($mB['price']) ? PHP_INT_MAX : $mB['price'];
                    if ($pA !== $pB) {
                        return $pA <=> $pB;
                    }
                    return $mB['avg_rating'] <=> $mA['avg_rating'];

                case 'price_high_low':
                    if ($mA['is_affiliate'] !== $mB['is_affiliate']) {
                        return $mB['is_affiliate'] <=> $mA['is_affiliate'];
                    }
                    $pA = is_null($mA['price']) ? -1 : $mA['price'];
                    $pB = is_null($mB['price']) ? -1 : $mB['price'];
                    if ($pA !== $pB) {
                        return $pB <=> $pA;
                    }
                    return $mB['avg_rating'] <=> $mA['avg_rating'];

                case 'name_a_z':
                    return strcmp($mA['name'], $mB['name']);

                case 'name_z_a':
                    return strcmp($mB['name'], $mA['name']);

                case 'highest_rated':
                default:
                    if ($mA['is_affiliate'] !== $mB['is_affiliate']) {
                        return $mB['is_affiliate'] <=> $mA['is_affiliate'];
                    }
                    if ($mA['avg_rating'] !== $mB['avg_rating']) {
                        return $mB['avg_rating'] <=> $mA['avg_rating'];
                    }
                    return $mB['review_count'] <=> $mA['review_count'];
            }
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
        if ($this->minPrice > $this->maxPrice) {
            $this->minPrice = $this->maxPrice;
        }
        $this->updatePriceFilterState();
        $this->resetPage();
        $this->dispatch('scroll-to-middle');
    }

    public function updatedMaxPrice()
    {
        if ($this->maxPrice < $this->minPrice) {
            $this->maxPrice = $this->minPrice;
        }
        $this->updatePriceFilterState();
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

    public function setSortBy($sort)
    {
        $this->sortBy = $sort;
        $this->showSortDropdown = false;
        $this->resetPage();
        $this->dispatch('scroll-to-middle');
    }

    public function toggleSortDropdown()
    {
        $this->showSortDropdown = !$this->showSortDropdown;
    }

    public function updatedSortBy()
    {
        $this->resetPage();
        $this->dispatch('scroll-to-middle');
    }

    public function clearFilters()
    {
        $this->selectedOptions = [];
        $this->searchTerm = '';
        $this->selectedRatings = [];
        $this->isPriceFilterActive = false;
        $this->sortBy = 'highest_rated';
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
    public function getExploreCategoriesProperty()
    {
        $lang_id = $this->lang_id ?: getCurrentLanguageID();
        $country_id = getCurrentCountry();

        $categories = Category::onlyParents()
            ->where('status', 1)
            ->where(function ($q) {
                $q->has('subCategories')->orWhereHas('businesses');
            })
            ->whereHas('translations', function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
            })
            ->withCount('businesses')
            ->orderByDesc('businesses_count')
            ->with([
                'translations' => function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id);
                }
            ])
            ->take(5)
            ->get();

        foreach ($categories as $cat) {
            $cat->top_businesses = $this->selectTopBusinessesForCategory($cat, $lang_id, $country_id);
        }

        return $categories;
    }

    protected function selectTopBusinessesForCategory($category, $lang_id, $country_id)
    {
        $subcategories = Category::where('parent_id', $category->id)
            ->where('status', 1)
            ->get();

        $subcatIds = $subcategories->pluck('id')->toArray();
        $allCatIds = array_merge([$category->id], $subcatIds);

        $businesses = Business::where(function ($q) use ($allCatIds) {
                $q->whereIn('category_id', $allCatIds)
                  ->orWhereHas('subCategories', function ($subQ) use ($allCatIds) {
                      $subQ->whereIn('categories.id', $allCatIds);
                  });
            })
            ->where('status', 1)
            ->whereHas('languages', function ($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            })
            ->whereHas('translations', function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
            })
            ->where(function ($q) use ($country_id) {
                $q->where('active_all_countries', 1)
                    ->orWhereHas('countries', function ($cq) use ($country_id) {
                        $cq->where('country_id', $country_id);
                    });
            })
            ->with([
                'translations' => function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id);
                },
                'subCategories' => function ($q) {
                    $q->select('categories.id');
                }
            ])
            ->withCount(['reviews as active_reviews_count' => function ($q) {
                $q->where('status', 'active');
            }])
            ->withAvg(['reviews as average_rating' => function ($q) {
                $q->where('status', 'active');
            }], 'rating')
            ->get();

        foreach ($businesses as $b) {
            $avgRating = $b->average_rating !== null ? (float) $b->average_rating : null;
            if ($avgRating !== null && $avgRating > 0) {
                $b->average_rating = round($avgRating, 1);
            } elseif ($b->admin_rating !== null && (float) $b->admin_rating > 0) {
                $b->average_rating = (float) $b->admin_rating;
            } else {
                $b->average_rating = 0.0;
            }
        }

        $sortComparator = function ($a, $b) {
            if ($a->average_rating != $b->average_rating) {
                return $b->average_rating <=> $a->average_rating;
            }
            if ($a->active_reviews_count != $b->active_reviews_count) {
                return $b->active_reviews_count <=> $a->active_reviews_count;
            }
            return $a->id <=> $b->id;
        };

        $buckets = [];
        if ($subcategories->isNotEmpty()) {
            foreach ($subcategories as $sub) {
                $subId = $sub->id;
                $bizInSub = $businesses->filter(function ($b) use ($subId) {
                    return $b->category_id == $subId || $b->subCategories->contains('id', $subId);
                });

                $aff = $bizInSub->filter(fn($b) => (int)$b->is_affiliate === 1)->sort($sortComparator)->values();
                $nonAff = $bizInSub->filter(fn($b) => (int)$b->is_affiliate !== 1)->sort($sortComparator)->values();

                $buckets[] = [
                    'id' => $subId,
                    'affiliated' => $aff,
                    'non_affiliated' => $nonAff,
                ];
            }

            $bizInParentOnly = $businesses->filter(function ($b) use ($category, $subcatIds) {
                $inSub = false;
                foreach ($subcatIds as $sId) {
                    if ($b->category_id == $sId || $b->subCategories->contains('id', $sId)) {
                        $inSub = true;
                        break;
                    }
                }
                return !$inSub && ($b->category_id == $category->id || $b->subCategories->contains('id', $category->id));
            });

            if ($bizInParentOnly->isNotEmpty()) {
                $aff = $bizInParentOnly->filter(fn($b) => (int)$b->is_affiliate === 1)->sort($sortComparator)->values();
                $nonAff = $bizInParentOnly->filter(fn($b) => (int)$b->is_affiliate !== 1)->sort($sortComparator)->values();
                $buckets[] = [
                    'id' => $category->id,
                    'affiliated' => $aff,
                    'non_affiliated' => $nonAff,
                ];
            }
        } else {
            $aff = $businesses->filter(fn($b) => (int)$b->is_affiliate === 1)->sort($sortComparator)->values();
            $nonAff = $businesses->filter(fn($b) => (int)$b->is_affiliate !== 1)->sort($sortComparator)->values();
            $buckets[] = [
                'id' => $category->id,
                'affiliated' => $aff,
                'non_affiliated' => $nonAff,
            ];
        }

        $selectedById = [];
        $maxAffRanks = 0;
        foreach ($buckets as $b) {
            $maxAffRanks = max($maxAffRanks, count($b['affiliated']));
        }

        for ($rank = 0; $rank < $maxAffRanks; $rank++) {
            if (count($selectedById) >= 6) {
                break;
            }
            foreach ($buckets as $b) {
                if (isset($b['affiliated'][$rank])) {
                    $biz = $b['affiliated'][$rank];
                    if (!isset($selectedById[$biz->id])) {
                        $selectedById[$biz->id] = $biz;
                    }
                }
            }
        }

        if (count($selectedById) < 6) {
            $maxNonAffRanks = 0;
            foreach ($buckets as $b) {
                $maxNonAffRanks = max($maxNonAffRanks, count($b['non_affiliated']));
            }

            for ($rank = 0; $rank < $maxNonAffRanks; $rank++) {
                if (count($selectedById) >= 6) {
                    break;
                }
                foreach ($buckets as $b) {
                    if (isset($b['non_affiliated'][$rank])) {
                        $biz = $b['non_affiliated'][$rank];
                        if (!isset($selectedById[$biz->id])) {
                            $selectedById[$biz->id] = $biz;
                        }
                    }
                }
            }
        }

        $selectedList = array_values($selectedById);

        $affSelected = collect($selectedList)
            ->filter(fn($b) => (int)$b->is_affiliate === 1)
            ->sort($sortComparator)
            ->values();

        $nonAffSelected = collect($selectedList)
            ->filter(fn($b) => (int)$b->is_affiliate !== 1)
            ->sort($sortComparator)
            ->values();

        return $affSelected->concat($nonAffSelected)->take(6);
    }

    public function getTextSectionsProperty()
    {
        $lang_id = $this->lang_id ?: getCurrentLanguageID();
        $content = \App\Models\TopProductContent::where('meta_key', 'top_rated_text_sections')
            ->where('lang_id', $lang_id)
            ->first();

        if ($content && !empty($content->meta_value)) {
            $decoded = json_decode($content->meta_value, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        return [
            [
                'h2_title' => 'How Localio ratings work',
                'h2_text' => '',
                'sub_sections' => [
                    [
                        'h3_title' => 'Why are these listings considered top rated?',
                        'h3_text' => 'Listings featured on this page represent the highest-rated solutions in their respective categories based on aggregated scores from authentic user reviews, overall satisfaction, and consistency over time.'
                    ],
                    [
                        'h3_title' => 'How Localio ratings work',
                        'h3_text' => 'Rankings on this page are based on ratings submitted by members of the Localio community. The order may also take factors such as the number of ratings into account, so a listing with a very small number of ratings does not automatically rank above one supported by substantially more community feedback.'
                    ]
                ]
            ],
            [
                'h2_title' => 'What you can discover on Localio',
                'h2_text' => "Localio brings community ratings and user reviews together across a broad range of categories. Explore everything from software and online services to local businesses, financial services, travel and much more.\n\nStart with the categories that interest you, compare what other community members have experienced and explore the listings that stand out.",
                'sub_sections' => []
            ]
        ];
    }

    public function getFaqsProperty()
    {
        $lang_id = $this->lang_id ?: getCurrentLanguageID();
        $content = \App\Models\TopProductContent::where('meta_key', 'top_rated_faqs')
            ->where('lang_id', $lang_id)
            ->first();

        if ($content && !empty($content->meta_value)) {
            $decoded = json_decode($content->meta_value, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        return [
            [
                'question' => 'How are top-rated products and businesses chosen?',
                'answer' => 'Top-rated listings are determined by verified community ratings, authentic review scores, user satisfaction metrics, and overall reliability across each industry category.'
            ],
            [
                'question' => 'How often are the top-rated rankings updated?',
                'answer' => 'Our rankings are updated continuously as new community reviews, verified feedback, and rating submissions are received.'
            ],
            [
                'question' => 'Can businesses pay to be featured as top-rated?',
                'answer' => 'No. Placement in top-rated rankings cannot be bought. Rankings strictly reflect actual community ratings and verified review performance.'
            ],
            [
                'question' => 'How can I submit a review for a business?',
                'answer' => 'Simply search for the business or visit its page on Localio, click "Write a review", rate the criteria, and share your experience.'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.top-rated-product', [
            'products' => $this->products,
            'filters' => $this->filters,
            'lang_id' => getCurrentLanguageID(),
            'exploreCategories' => $this->exploreCategories,
            'textSections' => $this->textSections,
            'faqs' => $this->faqs,
        ]);
    }
}
