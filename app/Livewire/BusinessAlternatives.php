<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\Category;
use App\Models\Log;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BusinessAlternatives extends Component
{
    use WithPagination;

    public $businessId;
    public $business;
    public $businessName;
    public $subCategoryIds = [];
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

    protected $queryString = [
        'selectedOptions' => ['except' => []],
        'searchTerm' => ['except' => ''],
        'selectedRatings' => ['except' => []],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 10000],
    ];

    public function mount($businessId, $initialPage = 1)
    {
        $this->businessId = $businessId;
        $this->page = (int) $initialPage;
        $this->lang_id = getCurrentLanguageID();

        $this->business = Business::with([
            'translations' => fn($q) => $q->where('lang_id', $this->lang_id),
            'products.categories'
        ])->findOrFail($businessId);

        $this->businessName = $this->business->translations->first()?->name ?? 'Business';

        // Get sub-categories
        $categoryIds = [$this->business->category_id];
        $productCategoryIds = $this->business->products
            ->flatMap(fn($product) => $product->categories->pluck('id'))
            ->toArray();
        $allCategoryIds = array_unique(array_filter(array_merge($categoryIds, $productCategoryIds)));

        $this->subCategoryIds = Category::whereIn('id', $allCategoryIds)
            ->whereNotNull('parent_id')
            ->pluck('id')
            ->toArray();

        if (empty($this->subCategoryIds) && $this->business->category_id) {
            $this->subCategoryIds = [$this->business->category_id];
        }

        // Load filters using all products of these sub-categories
        $allProducts = Product::with([
            'filterOptions.filterOption.filter.translations' => fn($q) => $q->where('language_id', $this->lang_id),
            'filterOptions.filterOption.translations' => fn($q) => $q->where('language_id', $this->lang_id),
            'filterOptions.filterOption.filter.filterType',
        ])
            ->where('lang_id', $this->lang_id)
            ->whereHas('translations', function ($q) {
                $q->where('lang_id', $this->lang_id);
            })
            ->whereHas('categories', function($cq) {
                $cq->whereIn('categories.id', $this->subCategoryIds);
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

    protected function buildFilters($products)
    {
        $filters = collect();

        foreach ($products as $product) {
            foreach ($product->filterOptions as $prodOption) {
                if ($prodOption->filterOption && $prodOption->filterOption->filter) {
                    $filter = $prodOption->filterOption->filter;

                    if (!$filters->has($filter->id)) {
                        $filter->options = collect();
                        $filters->put($filter->id, $filter);
                    }

                    $option = $prodOption->filterOption;
                    $existingFilter = $filters->get($filter->id);

                    if (!$existingFilter->options->has($option->id)) {
                        $existingFilter->options->put($option->id, $option);
                    }
                }
            }
        }

        return $filters->values();
    }

    protected function initializePriceRange()
    {
        $lang_id = $this->lang_id;
        $maxPrice = Product::whereHas('categories', function($cq) {
                $cq->whereIn('categories.id', $this->subCategoryIds);
            })
            ->where('lang_id', $lang_id)
            ->whereHas('prices')
            ->with('prices')
            ->get()
            ->flatMap(function ($product) {
                return $product->prices->map(function ($price) {
                    return $price->price;
                });
            })
            ->max();

        if ($maxPrice) {
            $this->maxPriceValue = (int) ceil($maxPrice);
            $this->maxPrice = $this->maxPriceValue;
            $this->dynamicMaxPrice = $this->maxPriceValue;
        } else {
            $this->maxPriceValue = 10000;
            $this->maxPrice = 10000;
            $this->dynamicMaxPrice = 10000;
        }
    }

    protected function loadDefaultFilterOptions()
    {
        if (!empty($this->selectedOptions)) {
            return;
        }

        foreach ($this->filters as $filter) {
            $filterType = $filter->filterType ? $filter->filterType->slug : 'checkbox';
            $defaultOptions = $filter->options->where('is_default', true);

            if ($defaultOptions->isNotEmpty()) {
                if (in_array($filterType, ['radio', 'dropdown']) && $defaultOptions->count() > 0) {
                    $this->selectedOptions[] = $defaultOptions->first()->id;
                } else {
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
        $languageObj = \App\Models\Language::where('lang_code', $locale)->first();
        $expectedAlternativesSlug = !empty($languageObj->alternatives_slug) ? $languageObj->alternatives_slug : 'alternatives';
        $businessSlug = $this->business->translations->first()->slug;

        $url = '/' . $locale . '/' . $businessSlug . '/' . $expectedAlternativesSlug;

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
        foreach ($this->filters as $filter) {
            $filterName = $filter->translations->first() ? $filter->translations->first()->name : $filter->name;
            $filterType = $filter->filterType ? $filter->filterType->slug : 'checkbox';

            $this->activeFilters[$filter->id] = [
                'name' => $filterName,
                'type' => $filterType,
                'options' => [],
                'display_order' => $filter->display_order ?? 1
            ];
        }
        uasort($this->activeFilters, function ($a, $b) {
            return ($a['display_order'] ?? 1) <=> ($b['display_order'] ?? 1);
        });
    }

    public function calculateRatingCounts()
    {
        $this->ratingCounts = [
            5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
        ];

        $businesses = Business::with([
            'reviews' => function ($q) {
                $q->where('status', 'active');
            }
        ])
            ->whereHas('languages', function ($query) {
                $query->where('language_id', $this->lang_id);
            })
            ->where('id', '!=', $this->businessId)
            ->where(function ($query) {
                $query->whereIn('category_id', $this->subCategoryIds)
                    ->orWhereHas('products', function($pq) {
                        $pq->whereHas('categories', function($cq) {
                            $cq->whereIn('categories.id', $this->subCategoryIds);
                        });
                    });
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
            ->withAvg(['reviews as avg_rating' => function ($q) {
                $q->where('status', 'active');
            }], 'rating')
            ->get();

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

    public function setPriceRange($data)
    {
        if (is_array($data) && isset($data['min']) && isset($data['max'])) {
            $this->minPrice = (int)$data['min'];
            $this->maxPrice = (int)$data['max'];
            $this->isPriceFilterActive = true;
            $this->resetPage();
        }
    }

    public function getProductsProperty()
    {
        $query = Business::whereHas('translations', function ($q) {
            $q->where('lang_id', $this->lang_id);
        })->whereHas('languages', function ($query) {
            $query->where('language_id', $this->lang_id);
        })
            ->where('id', '!=', $this->businessId)
            ->where(function ($query) {
                $query->whereIn('category_id', $this->subCategoryIds)
                    ->orWhereHas('products', function($pq) {
                        $pq->whereHas('categories', function($cq) {
                            $cq->whereIn('categories.id', $this->subCategoryIds);
                        });
                    });
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
            ->orderByDesc('avg_rating')
            ->orderBy('id');

        if (!empty($this->searchTerm)) {
            $query->whereHas('translations', function ($q) {
                $q->where('lang_id', $this->lang_id)
                    ->where(function ($sq) {
                        $sq->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        if (!empty($this->selectedRatings)) {
            $query->whereHas('reviews', function ($q) {
                $q->select('business_id')
                    ->where('status', 'active')
                    ->groupBy('business_id')
                    ->havingRaw('AVG(rating) >= ?', [min($this->selectedRatings)]);
            });
        }

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
                $business->icon_id = null;
            }
        });

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
            return $business->avg_rating ?? 0;
        })->values();

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
        foreach ($this->activeFilters as $filterId => $data) {
            $this->activeFilters[$filterId]['options'] = [];
        }

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
        return [];
    }

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

    public function toggleFilterOption($optionId)
    {
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

        switch ($filterType) {
            case 'radio':
                $this->selectedOptions = array_filter($this->selectedOptions, function ($id) use ($filterId) {
                    $filter = $this->filters->firstWhere('id', $filterId);
                    if (!$filter) return true;

                    foreach ($filter->options as $option) {
                        if ($option->id == $id) {
                            return false;
                        }
                    }
                    return true;
                });
                $this->selectedOptions[] = $optionId;
                break;

            case 'dropdown':
                $this->selectedOptions = array_filter($this->selectedOptions, function ($id) use ($filterId) {
                    $filter = $this->filters->firstWhere('id', $filterId);
                    if (!$filter) return true;

                    foreach ($filter->options as $option) {
                        if ($option->id == $id) {
                            return false;
                        }
                    }
                    return true;
                });
                $this->selectedOptions[$filterId] = $optionId;
                break;

            case 'toggle':
            case 'color':
            case 'checkbox':
            default:
                if (in_array($optionId, $this->selectedOptions)) {
                    $this->selectedOptions = array_diff($this->selectedOptions, [$optionId]);
                } else {
                    $this->selectedOptions[] = $optionId;
                }
                break;
        }

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

        foreach ($this->activeFilters as $filterId => $data) {
            $this->activeFilters[$filterId]['options'] = [];
        }

        $this->loadDefaultFilterOptions();
        $this->updateActiveFilters();
        $this->resetPage();
        $this->dispatch('scroll-to-middle');

        $locale = app()->getLocale();
        $languageObj = \App\Models\Language::where('lang_code', $locale)->first();
        $expectedAlternativesSlug = !empty($languageObj->alternatives_slug) ? $languageObj->alternatives_slug : 'alternatives';
        $businessSlug = $this->business->translations->first()->slug;

        return redirect()->to('/' . $locale . '/' . $businessSlug . '/' . $expectedAlternativesSlug);
    }

    public function render()
    {
        return view('livewire.business-alternatives', [
            'products' => $this->products,
            'filters' => $this->filters,
            'lang_id' => $this->lang_id,
        ]);
    }
}
