<?php

namespace App\Livewire\User;
use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\CategoryTranslation;
use Livewire\Component;

class SearchBar extends Component
{
    public $query = '';
    public $results = [];
    public $placeholder;

    public function mount($placeholder = 'Search for products or categories...')
    {
        $this->placeholder = $placeholder;
    }

    // This method is called whenever any property is updated
    public function updated($name, $value)
    {
        if ($name === 'query') {
            $this->performSearch();
        }
    }

    // This method is also called when query is updated (backup)
    public function updatedQuery()
    {
        $this->performSearch();
    }

    public function performSearch()
    {
        $lang_id = getCurrentLanguageID();

        if (!empty($this->query)) {
            // Get products
            $products = BusinessTranslation::where('lang_id', $lang_id)
                ->where('name', 'like', '%' . $this->query . '%')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'product',
                        'name' => $item->name,
                        'business_id' => $item->business_id
                    ];
                });

            // Get categories
            $categories = CategoryTranslation::where('lang_id', $lang_id)
                ->where('name', 'like', '%' . $this->query . '%')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'category',
                        'name' => $item->name,
                        'category_id' => $item->category_id
                    ];
                });

            // Combine and convert to array like your working code
            $this->results = $products->concat($categories)->toArray();
        } else {
            $this->results = [];
        }
    }

    public function redirectToProduct($businessId)
    {
        $lang_id = getCurrentLanguageID();
        $business = Business::where('id', $businessId)
            ->with(['translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            }])
            ->first();
        
        if ($business && $business->translations->isNotEmpty()) {
            $business_slug = $business->translations->first()->slug;
            return redirect()->route('product.details', [
                'locale' => app()->getLocale(), 
                'slug' => $business_slug
            ]);
        }
    }

    public function redirectToCategory($categoryId)
    {
        $lang_id = getCurrentLanguageID();
        $category = CategoryTranslation::where('category_id', $categoryId)
            ->where('lang_id', $lang_id)
            ->first();
        
        if ($category) {
            return redirect()->route('category.products', [
                'locale' => app()->getLocale(), 
                'slug' => $category->slug
            ]);
        }
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
    }

    public function render()
    {
        return view('livewire.user.search-bar');
    }
}
