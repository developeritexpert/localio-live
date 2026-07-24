<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategorySidebar extends Component
{
    public $categories;
    public $selectedCategoryId;
    public $subCategories = [];
    public $categoriesContents;

    public function mount($categories, $categoriesContents = [])
    {
        $this->categories = $categories;
        $this->categoriesContents = $categoriesContents;

        if ($categories->count() > 0) {
            $this->selectCategory($categories->first()->id);
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;

        $this->subCategories = Category::where('parent_id', $categoryId)
            ->where('status', 1)
            ->with(['translations', 'imageMedia', 'top_businesses.translations'])
            ->get();
    }

    public function render()
    {
        return view('livewire.category-sidebar');
    }
}