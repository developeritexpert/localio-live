<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategorySearch extends Component
{
    public $searchTerm = '';
    public $categories = [];
    public $searchResults = [];
    public $lang_id;

    public function mount()
    {
        $this->lang_id = getCurrentLanguageID();
        $this->loadCategories();
    }

    public function updated($name, $value)
    {
        if ($name === 'searchTerm') {
            $this->performSearch();
        }
    }

    public function updatedSearchTerm()
    {
        $this->performSearch();
    }



    public function performSearch()
    {
        if (!empty($this->searchTerm)) {
            $this->searchResults = Category::with('translations') 
                ->whereHas('translations', function ($query) {
                    $query->where('name', 'like', '%' . $this->searchTerm . '%')
                        ->where('lang_id', $this->lang_id);
                })
                ->limit(10)
                ->get()
                ->map(function ($category) {
                    $translation = $category->translations;
                    return [
                        'category_id' => $category->id,
                        'name' => $translation ? $translation->name : 'N/A',
                        'slug' => $translation ? $translation->slug : ''
                    ];
                })
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }


        //     public function performSearch()
        // {
        //     if (!empty($this->searchTerm)) {
        //         $this->searchResults = Category::with(['translations' => function ($query) {
        //             $query->where('lang_id', $this->lang_id);
        //         }])
        //         ->whereHas('translations', function ($query) {
        //             $query->where('name', 'like', '%' . $this->searchTerm . '%')
        //                 ->where('lang_id', $this->lang_id);
        //         })
        //         ->limit(10)
        //         ->get()
        //         ->map(function ($category) {
        //             $translation = $category->translations->first(); // This will already be filtered by lang_id
        //             return [
        //                 'category_id' => $category->id,
        //                 'name'        => $translation?->name ?? 'N/A',
        //                 'slug'        => $translation?->slug ?? '',
        //             ];
        //         })
        //         ->toArray();
        //     } else {
        //         $this->searchResults = [];
        //     }
        // }



    public function redirectToCategory($categoryId)
    {
        $category = Category::where('id', $categoryId)->with(['translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->first();

        $category_slug = $category->translations->slug;
        return redirect()->route('category.detail', ['locale' => app()->getLocale(), 'slug' => $category_slug]);
    }




    // public function loadCategories()
    // {
    //     $this->categories = Category::with('translations')
    //         ->whereHas('translations', function ($query) {
    //             $query->where('lang_id', $this->lang_id);
    //         })
    //         ->with('media')
    //         ->get();
    // }


        public function loadCategories()
    {
        $this->categories = Category::with(['translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])
        ->with('media')
        ->whereHas('translations', function ($query) {
            $query->where('lang_id', $this->lang_id);
        })
        ->get()
        ->values();
    }




    public function render()
    {
        return view('livewire.category-search', [
            'categories' => $this->categories,
        ]);
    }
}
