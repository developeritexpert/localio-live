<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\Product;
use App\Models\ProductTranslation;
use Livewire\Component;

class SearchBar extends Component
{
    public $query = '';       // Holds the user's search input
    public $results = [];     // Holds live search results
    public $placeholder;      // Custom placeholder text

    public function mount($placeholder = 'Search...')
    {
        $this->placeholder = $placeholder;
    }

    public function updated($name, $value)
{
    if ($name === 'query') {
        $this->performSearch();  // Call search when $query is updated
    }
}

    public function updatedQuery()
    {
        $this->performSearch();  // Live search on typing
    }

    public function performSearch()
    {
        $lang_id = getCurrentLanguageID();

        if (!empty($this->query)) {
            $this->results = BusinessTranslation::where('lang_id', $lang_id)
                ->where('name', 'like', '%' . $this->query . '%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->results = [];  // Clear results if input is empty
        }
    }
    public function redirectToProduct($productId)
    {
            $lang_id=getCurrentLanguageID();
        $Business=Business::where('id',$productId)->with(['translations'=> function ($query) use ($lang_id){
            $query->where('lang_id', $lang_id);
        } ])->first();
    $busienss_slug=$Business->translations->first()->slug;
        // dd($product_slug);
        return redirect()->route('product.details', ['locale' => app()->getLocale(),'slug' => $busienss_slug]);  // Redirect to product details page
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}
