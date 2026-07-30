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
            $subcatIds = Category::where('parent_id', $cat->id)->pluck('id')->toArray();
            $allCatIds = array_merge([$cat->id], $subcatIds);

            $businesses = Business::whereIn('category_id', $allCatIds)
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
                ->with(['translations' => function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id);
                }])
                ->withCount(['reviews as active_reviews_count' => function ($q) {
                    $q->where('status', 'active');
                }])
                ->withAvg(['reviews as average_rating' => function ($q) {
                    $q->where('status', 'active');
                }], 'rating')
                ->orderBy('average_rating', 'desc')
                ->take(6)
                ->get();

            $cat->top_businesses = $businesses;
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
                $businesses = Business::where('category_id', $subcat->id)
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
                    ->with(['translations' => function ($q) use ($lang_id) {
                        $q->where('lang_id', $lang_id);
                    }])
                    ->withCount(['reviews as active_reviews_count' => function ($q) {
                        $q->where('status', 'active');
                    }])
                    ->withAvg(['reviews as average_rating' => function ($q) {
                        $q->where('status', 'active');
                    }], 'rating')
                    ->orderBy('average_rating', 'desc')
                    ->take(6)
                    ->get();

                $subcat->top_businesses = $businesses;
                return $subcat;
            });
    }

    public function render()
    {
        return view('livewire.category-sidebar');
    }
}