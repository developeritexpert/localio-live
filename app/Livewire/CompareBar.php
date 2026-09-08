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
        $productIds = array_values(app(CompareService::class)->getComparedProducts());
        
        // If we have products and a categoryId is provided, validate they belong to this category family
        if (!empty($productIds) && !empty($productIds[0]) && $this->categoryId) {
            $firstProduct = \App\Models\Business::find($productIds[0]);
            if ($firstProduct) {
                $pCategory = \App\Models\Category::find($firstProduct->category_id);
$currentCategory = \App\Models\Category::find($this->categoryId);

$isValidCategory = false;

if ($pCategory && $currentCategory) {
    $isValidCategory =
        // Same category
        $firstProduct->category_id == $this->categoryId

        // Product is assigned to child, current page is parent
        || $pCategory->parent_id == $this->categoryId

        // Product is assigned to parent, current page is child
        || $currentCategory->parent_id == $firstProduct->category_id;
}

if (!$isValidCategory) {
    session()->forget('compared_products');
    $productIds = [];
}
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
        // Try to construct the SEO URL if 2 to 4 products are selected
        if (count($this->comparedProductIds) >= 2 && count($this->comparedProductIds) <= 4) {
            $lang_id = function_exists('getCurrentLanguageID') ? getCurrentLanguageID() : \App\Models\Language::where('lang_code', app()->getLocale())->value('id');
            
            $businesses = \App\Models\Business::with([
                'translations' => function($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id);
                }
            ])
            ->whereIn('id', $this->comparedProductIds)
            ->get()
            ->sortBy(function($model) {
                return array_search($model->id, $this->comparedProductIds);
            })
            ->values();
            
            if ($businesses->count() >= 2) {
                $categoryId = $businesses->first()->category_id;
                
                $categoryTranslation = \App\Models\CategoryTranslation::where('category_id', $categoryId)
                    ->where('lang_id', $lang_id)
                    ->first();
                
                $comparisonSlug = $categoryTranslation ? $categoryTranslation->comparison_slug : null;
                
                $allHaveSlugs = $businesses->every(function($b) use ($lang_id) {
                    return !empty($b->translations->firstWhere('lang_id', $lang_id)?->slug ?? $b->translations->first()?->slug);
                });

                if (!empty($comparisonSlug) && $allHaveSlugs) {
                    $vs_keyword = static_text('vs_keyword');
                    if (empty($vs_keyword) || $vs_keyword === 'vs_keyword') {
                        $vs_keyword = 'vs';
                    }
                    $vs_keyword = \Illuminate\Support\Str::slug($vs_keyword);
                    
                    $slugs = $businesses->map(function($b) use ($lang_id) {
                        return $b->translations->firstWhere('lang_id', $lang_id)?->slug ?? $b->translations->first()?->slug;
                    })->toArray();
                    
                    $comparisonBusinesses = implode("-{$vs_keyword}-", $slugs);
                    
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
