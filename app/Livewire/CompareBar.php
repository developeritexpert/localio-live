<?php
namespace App\Livewire;

use Livewire\Component;
use App\Services\CompareService;

class CompareBar extends Component
{
    public $comparedProducts = [];
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
        $products = app(CompareService::class)->getComparedProducts();
        
        // If we have products and a categoryId is provided, validate they belong to this category
        if (count($products) > 0 && $this->categoryId) {
            $firstProduct = \App\Models\Business::find($products[0]);
            if ($firstProduct && $firstProduct->category_id != $this->categoryId) {
                // If they belong to a different category, clear the session
                session()->forget('compared_products');
                $products = [];
            }
        }
        
        $this->comparedProducts = $products;
    }

    public function goToComparison()
    {
        // Try to construct the SEO URL if exactly 2 products are selected
        if (is_array($this->comparedProducts) && count($this->comparedProducts) === 2) {
            $lang_id = function_exists('getCurrentLanguageID') ? getCurrentLanguageID() : \App\Models\Language::where('lang_code', app()->getLocale())->value('id');
            
            $businesses = \App\Models\Business::with([
                'translations' => function($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id);
                }
            ])
            ->whereIn('id', $this->comparedProducts)
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
                    
                    $vs_keyword = __('messages.vs') !== 'messages.vs' ? __('messages.vs') : 'vs'; 
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
