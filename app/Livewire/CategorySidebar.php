<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\Category;
use Livewire\Component;

class CategorySidebar extends Component
{
    public $categories;
    public $selectedCategoryId = null;
    public $subCategories = [];
    public $categoriesContents;

    public function mount($categories, $categoriesContents = [])
    {
        $this->categories = $categories;
        $this->categoriesContents = $categoriesContents;

        $this->selectAllCategories();
    }

    public function selectAllCategories()
    {
        $this->selectedCategoryId = null;
        $lang_id = getCurrentLanguageID();
        $country_id = getCurrentCountry();

        $parents = Category::onlyParents()
            ->where('status', 1)
            ->where(function ($q) {
                $q->has('subCategories')->orWhereHas('businesses');
            })
            ->whereHas('translations', function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
            })
            ->with(['translations' => function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id);
            }, 'imageMedia'])
            ->get();

        $this->subCategories = $parents->map(function ($cat) use ($lang_id, $country_id) {
            $cat->top_businesses = $this->selectTopBusinessesForCategory($cat, $lang_id, $country_id);
            return $cat;
        });
    }

    public function selectCategory($categoryId)
    {
        if (is_null($categoryId)) {
            $this->selectAllCategories();
            return;
        }

        $this->selectedCategoryId = $categoryId;
        $lang_id = getCurrentLanguageID();
        $country_id = getCurrentCountry();

        $this->subCategories = Category::where('parent_id', $categoryId)
            ->where('status', 1)
            ->whereHas('translations', function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
            })
            ->with(['translations' => function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id);
            }, 'imageMedia'])
            ->get()
            ->map(function ($subcat) use ($lang_id, $country_id) {
                $subcat->top_businesses = $this->selectTopBusinessesForCategory($subcat, $lang_id, $country_id);
                return $subcat;
            });
    }

    /**
     * Select up to 6 top businesses for a category box:
     * - Round-robin best rated affiliated businesses across subcategories (1st best, 2nd best, etc.) until having at least 6
     * - If less than 6, round-robin non-affiliated businesses across subcategories until having at least 6
     * - Within the box, ordered: First affiliated by rating DESC, then Non-affiliated by rating DESC (maximum 6)
     */
    protected function selectTopBusinessesForCategory($category, $lang_id, $country_id)
    {
        // 1. Get subcategories of the category
        $subcategories = Category::where('parent_id', $category->id)
            ->where('status', 1)
            ->get();

        $subcatIds = $subcategories->pluck('id')->toArray();
        $allCatIds = array_merge([$category->id], $subcatIds);

        // 2. Fetch all eligible businesses in this category and its subcategories
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

        // 3. Compute effective display rating on each business
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

        // Sorting comparator: rating DESC, then active review count DESC, then ID ASC
        $sortComparator = function ($a, $b) {
            if ($a->average_rating != $b->average_rating) {
                return $b->average_rating <=> $a->average_rating; // DESC
            }
            if ($a->active_reviews_count != $b->active_reviews_count) {
                return $b->active_reviews_count <=> $a->active_reviews_count; // DESC
            }
            return $a->id <=> $b->id;
        };

        // 4. Build subcategory buckets
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

            // Direct businesses under parent category (if any not already in subcategories)
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
            // No subcategories, category itself is the single bucket
            $aff = $businesses->filter(fn($b) => (int)$b->is_affiliate === 1)->sort($sortComparator)->values();
            $nonAff = $businesses->filter(fn($b) => (int)$b->is_affiliate !== 1)->sort($sortComparator)->values();
            $buckets[] = [
                'id' => $category->id,
                'affiliated' => $aff,
                'non_affiliated' => $nonAff,
            ];
        }

        $selectedById = [];

        // 5. Select affiliated businesses round-robin across subcategories (1st best, 2nd best, etc.)
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

        // 6. If less than 6, select non-affiliated businesses round-robin across subcategories
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

        // 7. Final ordering within the box: First affiliated by rating DESC, then non-affiliated by rating DESC (max 6)
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

    public function render()
    {
        return view('livewire.category-sidebar', [
            'categories' => $this->categories,
            'subCategories' => $this->subCategories,
        ]);
    }
}
