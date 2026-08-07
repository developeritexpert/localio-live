<?php
namespace App\Livewire;

use Livewire\Component;
use App\Services\CompareService;

class CompareBar extends Component
{
    public $comparedProducts = []; // Array of Business models
    public $comparedProductIds = []; // Array of IDs
    public $errorMessage = '';
    public $categoryId = null;

    protected $listeners = ['toggleCompareProduct' => 'refreshComparedProducts'];

    public function mount($categoryId = null)
    {
        $this->categoryId = $categoryId;
        $this->refreshComparedProducts();
    }

    public function refreshComparedProducts()
    {
        $productIds = app(CompareService::class)->getComparedProducts();
        
        // If we have products and a categoryId is provided, validate they belong to this category
        if (count($productIds) > 0 && $this->categoryId) {
            $firstProduct = \App\Models\Business::find($productIds[0]);
            if ($firstProduct && $firstProduct->category_id != $this->categoryId) {
                // If they belong to a different category, clear the session
                session()->forget('compared_products');
                $productIds = [];
            }
        }
        
        $this->comparedProductIds = $productIds;

        if (count($productIds) > 0) {
            $lang_id = function_exists('getCurrentLanguageID') ? getCurrentLanguageID() : \App\Models\Language::where('lang_code', app()->getLocale())->value('id');
            $this->comparedProducts = \App\Models\Business::with([
                'translations' => function($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id);
                }
            ])
            ->whereIn('id', $productIds)
            ->get()
            ->sortBy(function($model) use ($productIds) {
                return array_search($model->id, $productIds);
            })
            ->values();
        } else {
            $this->comparedProducts = collect();
        }
    }

    public function removeProduct($productId)
    {
        $result = app(CompareService::class)->toggleProductComparison($productId);
        $this->refreshComparedProducts();
        $this->dispatch('toggleCompareProduct');
    }

    public function clearAll()
    {
        session()->forget('compared_products');
        $this->refreshComparedProducts();
        $this->dispatch('toggleCompareProduct');
    }

    public function goToComparison()
    {
        // Try to construct the SEO URL if exactly 2 products are selected
        if (count($this->comparedProductIds) === 2) {
            $lang_id = function_exists('getCurrentLanguageID') ? getCurrentLanguageID() : \App\Models\Language::where('lang_code', app()->getLocale())->value('id');
            
            $businesses = \App\Models\Business::with([
                'translations' => function($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id);
                }
            ])
            ->whereIn('id', $this->comparedProductIds)
            ->get();
            
            if ($businesses->count() === 2) {
                // Get the category of the first business
                $categoryId = $businesses->first()->category_id;
                
                $categoryTranslation = \App\Models\CategoryTranslation::where('category_id', $categoryId)
                    ->where('lang_id', $lang_id)
                    ->first();
                
                $comparisonSlug = $categoryTranslation ? $categoryTranslation->comparison_slug : null;
                
                // Only use the new SEO route if a comparison slug is defined and business slugs exist
                if (!empty($comparisonSlug) && $businesses[0]->translations->first() && $businesses[1]->translations->first()) {
                    $slug1 = $businesses[0]->translations->first()->slug;
                    $slug2 = $businesses[1]->translations->first()->slug;
                    
                    $vs_keyword = static_text('vs_keyword');
                    if (empty($vs_keyword) || $vs_keyword === 'vs_keyword') {
                        $vs_keyword = 'vs';
                    }
                    $vs_keyword = \Illuminate\Support\Str::slug($vs_keyword);
                    $comparisonBusinesses = "{$slug1}-{$vs_keyword}-{$slug2}";
                    
                    // Clear the session so the comparison bar doesn't stick around after successful navigation
                    session()->forget('compared_products');
                    
                    return redirect()->route('product-comparison.seo', [
                        'locale' => app()->getLocale(),
                        'comparison_slug' => $comparisonSlug,
                        'comparison_businesses' => $comparisonBusinesses
                    ]);
                }
            }
        }

        // Fallback to old route if SEO route cannot be built. Also clear it to prevent sticky state.
        session()->forget('compared_products');
        return redirect()->route('product-comparison', ['locale' => app()->getLocale()]);
    }

    public function render()
    {
        return view('livewire.compare-bar');
    }
}
