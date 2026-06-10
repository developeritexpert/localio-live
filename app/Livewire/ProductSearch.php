<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\Product;
use Livewire\Component;

class ProductSearch extends Component
{
    public $searchTerm = '';     // Search term entered by the user
    public $businesses = [];     // Store the main businesses here
    public $searchResults = [];  // Store search dropdown results
    public $lang_id;            // Store the language ID
    public $country_id;

    // Initialize the component, fetching the current language ID
    public function mount()
    {
        $this->lang_id = getCurrentLanguageID(); // Get the language ID based on the current language
        $this->country_id = getCurrentCountry();
        $this->loadbusinesses();  // Initial load of products
    }

    public function updated($name, $value)
    {
        if ($name === 'searchTerm') {
            $this->performSearch();  // Call search when searchTerm is updated
        }
    }

    public function updatedSearchTerm()
    {
        $this->performSearch();  // Live search on typing
    }

    public function performSearch()
    {
        if (!empty($this->searchTerm)) {
            $this->searchResults = Business::with('translations')
                ->whereHas('translations', function ($query) {
                    $query->where('name', 'like', '%' . $this->searchTerm . '%')
                        ->where('lang_id', $this->lang_id);
                })
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
                })
                ->limit(10)
                ->get()
                ->map(function ($business) {
                    $translation = $business->translations->firstWhere('lang_id', $this->lang_id);
                    return [
                        'business_id' => $business->id,
                        'name' => $translation ? $translation->name : 'N/A'
                    ];
                })
                ->toArray();
        } else {
            $this->searchResults = [];  // Clear results if input is empty
        }
    }


    public function redirectToProduct($productId)
    {
        $Business = Business::where('id', $productId)->with(['translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->first();

        $business_slug = $Business->translations->first()->slug;
        return redirect()->route('product.details', ['locale' => app()->getLocale(), 'slug' => $business_slug]);  // Redirect to product details page
    }

    // Method to load products based on the search term (original functionality)
    public function loadbusinesses()
    {
        $this->businesses = Business::with('translations')
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
            })
            ->withAvg(['reviews as avg_rating' => function ($q) {
                $q->where('status', 'active');
            }], 'rating')
            ->orderByDesc('avg_rating')
            ->take(10)
            ->get();
    }

    // Render the Livewire component and pass the products to the view
    public function render()
    {
        return view('livewire.product-search', [
            'businesses' => $this->businesses,
        ]);
    }
}
