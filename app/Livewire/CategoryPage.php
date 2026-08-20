<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\Category;
use App\Models\Filter;
use App\Models\FilterType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
    public $subCategories = [];
    public $selectedSubCategories = [];

    // Filter properties
    public $minPrice = 0;
    public $maxPrice = 5000;
    public $maxPriceValue = 5000;
    public $selectedOptions = [];
    public $selectedRatings = [];
    public $selectedCriteriaRatings = [];
    public $availableCriteria = [];
    public $filters = [];
    public $filterTypes = [];
    public $activeFilters = [];
    public $isPriceFilterActive = false;
    public $ratingCounts;
    public $sortBy = 'highest_rated';
    public $showSortDropdown = false;

    // Results properties
    public $productsCount = 0;
    public $noMatchingProducts = false;
    public $page = 1;

    // Configure URL parameters - page is now path-based
    protected $queryString = [
        'selectedOptions' => ['except' => []],
        'selectedSubCategories' => ['except' => []],
        'searchTerm' => ['except' => ''],
        'selectedRatings' => ['except' => []],
        'selectedCriteriaRatings' => ['except' => []],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 2000],
        'sortBy' => ['except' => 'highest_rated'],
    ];

    public $feature_slug = null;

    public function mount($slug, $initialPage = 1, $feature_slug = null)
    {
        $this->slug = $slug;
        $this->feature_slug = $feature_slug;
        $this->page = (int) $initialPage;
        $this->lang_id = getCurrentLanguageID();
        $this->country_id= getCurrentCountry();
        // Get category data
        $this->category = Category::whereHas('translations', function ($query) {
            $query->where('slug', $this->slug)
                ->where('lang_id', $this->lang_id);
        })->with(['translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }, 'parent.translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->firstOrFail();

        $this->availableCriteria = $this->category->getEffectiveRatingCriteria();

        // Clear comparison session if switching to a different category
        $comparedProducts = session()->get('compared_products', []);
        if (count($comparedProducts) > 0) {
            $firstProduct = \App\Models\Business::find($comparedProducts[0]);
            if ($firstProduct && $firstProduct->category_id != $this->category->id) {
                session()->forget('compared_products');
            }
        }

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

        // Set the page in the paginator
        if ($this->page > 1) {
            $this->setPage($this->page);
        }

        // Check if this is a parent category
        if (is_null($this->category->parent_id)) {
            $this->loadParentCategoryData();
            if (count($this->subCategories) > 0) {
                $this->isParentCategory = true;
            }
        }
    }

    protected function loadParentCategoryData()
    {
        $this->subCategories = Category::where('parent_id', $this->category->id)
            ->with(['translations' => function ($query) {
                $query->where('lang_id', $this->lang_id);
            }])
            ->get();
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
                'options',
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

    public function updatedSelectedSubCategories()
    {
        $this->calculateRatingCounts();
        $this->resetPage();
        $this->dispatch('scroll-to-middle');
    }

    public function calculateRatingCounts()
    {
        if ($this->isParentCategory) {
            $allSubcatIds = Category::where('parent_id', $this->category->id)->pluck('id')->toArray();
            $allCategoryIds = array_merge([$this->category->id], $allSubcatIds);
            if (!empty($this->selectedSubCategories)) {
                $targetCategoryIds = array_intersect($allCategoryIds, array_map('intval', (array)$this->selectedSubCategories));
            } else {
                $targetCategoryIds = $allCategoryIds;
            }
            $businesses = Business::select('id')
                ->whereIn('category_id', $targetCategoryIds);
        } else {
            $businesses = Business::select('id')
                ->where('category_id', $this->category->id);
        }

        $businesses = $businesses->where('status', 1)
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

    public function getTopAffiliatedBusinessIdProperty()
    {
        if ($this->isParentCategory) {
            $allSubcatIds = Category::where('parent_id', $this->category->id)->pluck('id')->toArray();
            $targetCategoryIds = array_merge([$this->category->id], $allSubcatIds);
            $query = Business::whereIn('category_id', $targetCategoryIds);
        } else {
            $query = Business::where('category_id', $this->category->id);
        }

        return $query->where('status', 1)
            ->where('is_affiliate', 1)
            ->whereHas('languages', function ($q) {
                $q->where('language_id', $this->lang_id);
            })
            ->where(function ($q) {
                $q->where('active_all_countries', 1)
                    ->orWhereHas('countries', function ($cq) {
                        $cq->where('country_id', $this->country_id);
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
        // Start with businesses in this category or its subcategories if parent category
        if ($this->isParentCategory) {
            $allSubcatIds = Category::where('parent_id', $this->category->id)->pluck('id')->toArray();
            $allCategoryIds = array_merge([$this->category->id], $allSubcatIds);
            if (!empty($this->selectedSubCategories)) {
                $targetCategoryIds = array_intersect($allCategoryIds, array_map('intval', (array)$this->selectedSubCategories));
            } else {
                $targetCategoryIds = $allCategoryIds;
            }
            $query = Business::whereIn('category_id', $targetCategoryIds);
        } else {
            $query = Business::where('category_id', $this->category->id);
        }

        if (!empty($this->feature_slug)) {
            $featureSlug = $this->feature_slug;
            $query->whereHas('features', function ($q) use ($featureSlug) {
                $q->whereHas('translations', function ($tq) use ($featureSlug) {
                    $tq->where('name', 'LIKE', str_replace('-', ' ', $featureSlug))
                       ->orWhere('name', 'LIKE', $featureSlug);
                });
            });
        }

        $query->where('status', 1)
            ->with([
                'translations' => function ($q) {
                    $q->where('lang_id', $this->lang_id);
                },'limitedFeatures.translations'=>fn($q) => $q->where('lang_id', $this->lang_id),
                'products' => function ($pq) {
                    $pq->with(['prices' => fn($p) => $p->orderBy('price')])
                        ->where(function ($query) {
                            $query->where('active_all_countries', 1)
                                ->orWhere(function ($q) {
                                    $q->where('active_all_countries', 0)
                                        ->whereHas('countries', function ($countryQuery) {
                                            $countryQuery->where('country_id', $this->country_id);
                                        });
                                });
                        });

                    if ($this->category && $this->category->parent_id) {
                        $subCatId = $this->category->id;
                        $pq->where(function ($sq) use ($subCatId) {
                            $sq->where('active_all_subcategories', 1)
                               ->orWhere(function ($subq) use ($subCatId) {
                                   $subq->where('active_all_subcategories', 0)
                                        ->whereHas('categories', function ($catQ) use ($subCatId) {
                                            $catQ->where('categories.id', $subCatId);
                                        });
                               });
                        });
                    }
                },
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

        // Apply criteria rating filter if selected
        if (!empty($this->selectedCriteriaRatings)) {
            foreach ($this->selectedCriteriaRatings as $criteriaId => $minRating) {
                if (!empty($minRating) && (int)$minRating > 0) {
                    $minRating = (int)$minRating;
                    $query->whereIn('id', function ($subQuery) use ($criteriaId, $minRating) {
                        $subQuery->select('reviews.business_id')
                            ->from('reviews')
                            ->join('review_ratings', 'reviews.id', '=', 'review_ratings.review_id')
                            ->where('reviews.status', 'active')
                            ->where('review_ratings.criteria_id', $criteriaId)
                            ->groupBy('reviews.business_id')
                            ->havingRaw('AVG(review_ratings.rating) >= ?', [$minRating]);
                    });
                }
            }
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
            })->filter(fn($p) => !is_null($p)); // Remove nulls

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

        // Count before pagination
        $totalCount = $filtered->count();
        $this->productsCount = $totalCount;
        $this->noMatchingProducts = $totalCount === 0;

        // Paginate manually using Laravel collection
        $currentPage = max(1, $this->page);
        $perPage = 10;
        $maxPage = max(1, ceil($totalCount / $perPage));
        if ($currentPage > $maxPage) {
            $currentPage = $maxPage;
            $this->page = $currentPage;
        }
        $paginated = $filtered->forPage($currentPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginated,
            $totalCount,
            $perPage,
            $currentPage,
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

    public function resetFilters()
    {
        $this->selectedOptions = [];
        $this->selectedSubCategories = [];
        $this->searchTerm = '';
        $this->selectedRatings = [];
        $this->isPriceFilterActive = false;
        $this->sortBy = 'highest_rated';
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
        $perPage = 10;
        $totalPages = ceil($this->productsCount / $perPage);
        if ($this->page < $totalPages) {
            $this->page++;
            $this->setPage($this->page);
            $this->dispatchPaginationUrl();
            $this->dispatch('scroll-to-middle');
        }
    }

    public function gotoPage($page)
    {
        $this->page = (int) $page;
        $this->setPage($this->page);
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
            $url = '/' . $locale . '/' . $this->slug . '/' . $page;
        } else {
            $url = '/' . $locale . '/' . $this->slug;
        }

        // Append filter query params
        $queryParams = [];
        if (!empty($this->selectedRatings)) $queryParams['selectedRatings'] = $this->selectedRatings;
        if (!empty($this->selectedSubCategories)) $queryParams['selectedSubCategories'] = $this->selectedSubCategories;
        if (!empty($this->selectedOptions)) $queryParams['selectedOptions'] = $this->selectedOptions;
        if ($this->searchTerm !== '') $queryParams['searchTerm'] = $this->searchTerm;
        if ($this->minPrice != 0) $queryParams['minPrice'] = $this->minPrice;
        if ($this->maxPrice != 2000) $queryParams['maxPrice'] = $this->maxPrice;

        return empty($queryParams) ? $url : $url . '?' . http_build_query($queryParams);
    }


    public function getTextSectionsProperty()
    {
        $trans = $this->category->translations;
        if ($trans && !empty($trans->text_sections)) {
            $decoded = is_array($trans->text_sections) ? $trans->text_sections : json_decode($trans->text_sections, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        $catName = $trans->name ?? $this->category->name ?? 'this category';
        return [
            [
                'h2_title' => 'About ' . strtolower($catName),
                'h2_text' => 'Explore the leading providers and solutions in ' . strtolower($catName) . '. Compare key features, verified reviews, pricing, and ratings to make informed choices.',
                'sub_sections' => [
                    [
                        'h3_title' => 'What is ' . strtolower($catName) . '?',
                        'h3_text' => $catName . ' offers specialized services and technology designed to solve industry challenges, optimize workflows, and deliver measurable results.'
                    ],
                    [
                        'h3_title' => 'How does ' . strtolower($catName) . ' work?',
                        'h3_text' => 'Providers deliver tailored capabilities with varying tiers of support, integration, and features to meet different organizational or individual needs.'
                    ],
                    [
                        'h3_title' => 'Types of ' . strtolower($catName),
                        'h3_text' => 'Different solutions range from entry-level and self-service tools to enterprise-grade platforms and managed services.'
                    ]
                ]
            ],
            [
                'h2_title' => 'What to consider when comparing ' . strtolower($catName) . ' providers',
                'h2_text' => 'When evaluating options, consider factors such as feature set, pricing transparency, reliability, customer feedback, and ease of integration.',
                'sub_sections' => []
            ]
        ];
    }

    public function getPopularComparisonsProperty()
    {
        $lang_id = $this->lang_id;
        $catIds = [$this->category->id];
        if ($this->category->parent_id === null) {
            $childIds = Category::where('parent_id', $this->category->id)->pluck('id')->toArray();
            $catIds = array_merge($catIds, $childIds);
        }

        $topBusinesses = Business::where(function ($q) use ($catIds) {
                $q->whereIn('category_id', $catIds)
                  ->orWhereHas('subCategories', function ($subQ) use ($catIds) {
                      $subQ->whereIn('categories.id', $catIds);
                  });
            })
            ->where('status', 1)
            ->whereHas('translations', function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
            })
            ->withCount(['reviews as active_reviews_count' => function ($q) {
                $q->where('status', 'active');
            }])
            ->withAvg(['reviews as average_rating' => function ($q) {
                $q->where('status', 'active');
            }], 'rating')
            ->with(['translations' => function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id);
            }])
            ->orderByDesc('active_reviews_count')
            ->orderByDesc('average_rating')
            ->take(4)
            ->get();

        $comparisons = [];
        $count = count($topBusinesses);
        if ($count >= 2) {
            $compSlug = $this->category->translations->comparison_slug ?? 'compare';
            $vsKey = static_text('vs_keyword') ?: 'vs';
            if (empty($vsKey) || $vsKey === 'vs_keyword') $vsKey = 'vs';
            $vsKeySlug = Str::slug($vsKey);

            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    if (count($comparisons) >= 4) break 2;
                    $b1 = $topBusinesses[$i];
                    $b2 = $topBusinesses[$j];
                    $b1Name = $b1->translations->first()->name ?? $b1->name ?? '';
                    $b2Name = $b2->translations->first()->name ?? $b2->name ?? '';

                    if (!empty($b1Name) && !empty($b2Name)) {
                        $comparisons[] = [
                            'business_1' => $b1,
                            'business_1_name' => $b1Name,
                            'business_1_rating' => $b1->average_rating ?? 0,
                            'business_2' => $b2,
                            'business_2_name' => $b2Name,
                            'business_2_rating' => $b2->average_rating ?? 0,
                            'url' => route('product-comparison.seo', [
                                'locale' => app()->getLocale(),
                                'comparison_slug' => $compSlug,
                                'comparison_businesses' => Str::slug($b1Name) . '-' . $vsKeySlug . '-' . Str::slug($b2Name)
                            ])
                        ];
                    }
                }
            }
        }

        return $comparisons;
    }

    public function getExploreSubcategoriesProperty()
    {
        $lang_id = $this->lang_id;
        if ($this->category->parent_id === null) {
            $subcats = Category::where('parent_id', $this->category->id)
                ->where('status', 1)
                ->whereHas('translations', function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
                })
                ->with(['translations' => function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id);
                }])
                ->withCount(['businesses' => function ($q) {
                    $q->where('status', 1);
                }])
                ->get();
            return [
                'title' => 'Explore ' . ($this->category->translations->name ?? $this->category->name ?? '') . ' categories',
                'items' => $subcats
            ];
        } else {
            $parent = $this->category->parent;
            $parentName = $parent ? ($parent->translations->name ?? $parent->name ?? '') : '';
            $subcats = Category::where('parent_id', $this->category->parent_id)
                ->where('id', '!=', $this->category->id)
                ->where('status', 1)
                ->whereHas('translations', function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
                })
                ->with(['translations' => function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id);
                }])
                ->withCount(['businesses' => function ($q) {
                    $q->where('status', 1);
                }])
                ->get();
            return [
                'title' => 'Explore other ' . (!empty($parentName) ? $parentName : ($this->category->translations->name ?? '')) . ' categories',
                'items' => $subcats
            ];
        }
    }

    public function getPopularBusinessesProperty()
    {
        $lang_id = $this->lang_id;
        $catIds = [$this->category->id];
        if ($this->category->parent_id === null) {
            $childIds = Category::where('parent_id', $this->category->id)->pluck('id')->toArray();
            $catIds = array_merge($catIds, $childIds);
        }

        return Business::where(function ($q) use ($catIds) {
                $q->whereIn('category_id', $catIds)
                  ->orWhereHas('subCategories', function ($subQ) use ($catIds) {
                      $subQ->whereIn('categories.id', $catIds);
                  });
            })
            ->where('status', 1)
            ->whereHas('translations', function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
            })
            ->withCount(['reviews as active_reviews_count' => function ($q) {
                $q->where('status', 'active');
            }])
            ->withAvg(['reviews as average_rating' => function ($q) {
                $q->where('status', 'active');
            }], 'rating')
            ->with(['translations' => function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id);
            }, 'products.prices', 'usps'])
            ->orderByDesc('is_affiliate')
            ->orderByDesc('average_rating')
            ->orderByDesc('active_reviews_count')
            ->take(6)
            ->get();
    }

    public function getFaqsProperty()
    {
        $trans = $this->category->translations;
        if ($trans && !empty($trans->faqs)) {
            $decoded = is_array($trans->faqs) ? $trans->faqs : json_decode($trans->faqs, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        $catName = $trans->name ?? $this->category->name ?? 'this category';
        return [
            [
                'question' => 'How do I choose the best ' . strtolower($catName) . ' provider?',
                'answer' => 'Compare essential features, user ratings, transparent pricing, and reliability. Assess whether the provider aligns with your specific technical and budgetary requirements.'
            ],
            [
                'question' => 'Are community ratings and reviews verified?',
                'answer' => 'Yes, reviews and ratings on Localio are submitted by verified users and evaluated for authenticity by our moderation team.'
            ],
            [
                'question' => 'How often are rankings and listings updated?',
                'answer' => 'Our listings and rating scores are updated dynamically as community members submit feedback and ratings.'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.category-page', [
            'products' => $this->products,
            'sortBy' => $this->sortBy,
            'showSortDropdown' => $this->showSortDropdown,
            'lang_id' => getCurrentLanguageID(),
            'textSections' => $this->textSections,
            'popularComparisons' => $this->popularComparisons,
            'exploreSubcategories' => $this->exploreSubcategories,
            'popularBusinesses' => $this->popularBusinesses,
            'faqs' => $this->faqs,
        ]);
    }
}
