<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class DealsSearch extends Component
{
    public $searchTerm = '';
    public $searchResults = [];

    public $countryId;
    public $lang_id;

    public function mount($countryId = null)
    {
        $this->countryId = getCurrentCountry();
        $this->lang_id = getCurrentLanguageID();
    }
    public function performSearch()
    {
        if (empty($this->searchTerm)) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Product::whereHas('prices', function ($query) {
            $query->whereNotNull('discount_price')
                ->whereDate('discount_expiration_date', '>=', now());
        })
            ->whereHas('translations', function ($query) {
                $query->where('lang_id', $this->lang_id)
                    ->where('product_translations.status', 'active')
                    ->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->whereHas('businesses', function ($query) {
                $query->where(function ($q) {
                    $q->where('active_all_countries', 1)
                        ->orWhereHas('countries', function ($q2) {
                            $q2->where('country_id', $this->countryId);
                        });
                })
                    ->whereHas('translations', function ($q) {
                        $q->where('lang_id', $this->lang_id);
                    })
                    ->whereHas('languages', function ($q) {
                        $q->where('language_id', $this->lang_id);
                    });
            })
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                    ->orWhereHas('countries', function ($countryQuery) {
                        $countryQuery->where('country_id', $this->countryId);
                    });
            })
            ->where('products.status', 'public')
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'prices' => fn($q) => $q->whereNotNull('discount_price')
                    ->whereDate('discount_expiration_date', '>=', now())
                    ->orderBy('price'),
                'businesses.translations' => fn($q) => $q->where('lang_id', $this->lang_id),
            ])
            ->take(10)
            ->get()
            ->map(function ($product) {
                $price = $product->prices->first();
                $discount_percentage = $price && $price->price > 0
                    ? round((($price->price - $price->discount_price) / $price->price) * 100)
                    : 0;

                return [
                    'product_id' => $product->id,
                    'name' => $product->translations->name ?? 'Untitled Product',
                    'image' => $product->product_icon,
                    'discount_percentage' => $discount_percentage,
                ];
            })
            ->toArray();
    }
    public function redirectToProduct($productId)
    {
        $product = Product::with(['businesses.translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->find($productId);

        if ($product && $product->businesses->first()) {
            $business = $product->businesses->first();
            $business_slug = $business->translations->first()->slug ?? 'business';

            return redirect()->route('product.details', ['locale' => app()->getLocale(), 'slug' => $business_slug]);
        }
    }

    public function render()
    {
        $exclusive_products = Product::whereHas('prices', function ($q) {
                $q->whereNotNull('discount_price')
                  ->whereDate('discount_expiration_date', '>=', now());
            })
            ->where('products.status', 'public')
            ->whereHas('businesses', function ($query) {
                $query->where(function ($q) {
                        $q->where('active_all_countries', 1)
                          ->orWhereHas('countries', function ($q2) {
                              $q2->where('country_id', $this->countryId);
                          });
                    })
                    ->whereHas('translations', function ($q) {
                        $q->where('lang_id', $this->lang_id);
                    })
                    ->whereHas('languages', function ($q) {
                        $q->where('language_id', $this->lang_id);
                    });
            })
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                      ->orWhereHas('countries', function ($countryQuery) {
                          $countryQuery->where('country_id', $this->countryId);
                      });
            })
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'prices' => fn($q) => $q->whereNotNull('discount_price')->orderBy('price'),
                'businesses.translations' => fn($q) => $q->where('lang_id', $this->lang_id),
            ])
            ->get();

        $top_discounted_products = $exclusive_products->map(function ($product) {
                $price = $product->prices->first();
                $discount_percentage = $price && $price->price > 0
                    ? round((($price->price - $price->discount_price) / $price->price) * 100)
                    : 0;

                return [
                    'product' => $product,
                    'discount_percentage' => $discount_percentage,
                ];
            })
            ->sortByDesc('discount_percentage')
            ->take(6)
            ->pluck('product')
            ->values();

        return view('livewire.deals-search', [
            'exclusive_products' => $top_discounted_products,
        ]);
    }
}
