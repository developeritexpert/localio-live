<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Business;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProductForm extends Component
{
    use WithFileUploads;

    public $product;
    public $name;
    public $product_link;
    public $overview;
    public $description;
    public $product_businesses = [];
    public $product_category = [];
    public $prices = [];
    public $tenures = [];
    public $product_icon;
    public $product_image;
    public $existingIcon;
    public $existingImage;
    public $lang_code;
    public $isEditing = false;
    public $isProcessing = false;
    
    // File upload trackers
    public $isUploadingIcon = false;
    public $isUploadingImage = false;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'businessesSelected' => 'setBusinesses',
        'categoriesSelected' => 'setCategories'
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'product_link' => 'required|url',
            'overview' => 'required|string',
            'description' => 'required|string',
            'product_businesses' => 'required|array|min:1',
            'product_category' => 'required|array|min:1',
            'prices' => 'required|array|min:1',
            'prices.*' => 'required|numeric|min:0',
            'tenures' => 'required|array|min:1',
            'tenures.*' => 'required|string|in:base_price,standard_price,pro_price',
            'product_icon' => $this->isEditing ? 'nullable|image|max:2048' : 'required|image|max:2048',
            'product_image' => $this->isEditing ? 'nullable|image|max:2048' : 'required|image|max:2048',
            'lang_code' => 'required'
        ];
    }

    protected $messages = [
        'product_businesses.required' => 'Please select at least one business',
        'product_category.required' => 'Please select at least one category',
        'prices.*.required' => 'Price is required',
        'prices.*.numeric' => 'Price must be a number',
        'tenures.*.required' => 'Price type is required',
    ];

    public function mount($product = null)
    {
        $this->lang_code = getCurrentLanguageID();
        
        if ($product) {
            $this->product = $product;
            $this->isEditing = true;
            $this->name = $product->translation->name ?? '';
            $this->product_link = $product->product_link ?? '';
            $this->overview = $product->translation->overview ?? '';
            $this->description = $product->translation->description ?? '';
            
            // Handle product businesses
            if ($product->businesses) {
                $this->product_businesses = $product->businesses->pluck('id')->toArray();
            }
            
            // Handle product categories
            if ($product->categories) {
                $this->product_category = $product->categories->pluck('id')->toArray();
            }
            
            // Load prices
            $this->loadProductPrices($product);
            
            // Set existing images
            $this->existingIcon = $product->product_icon;
            $this->existingImage = $product->product_image;
        } else {
            // Initialize with empty price row for new products
            $this->prices = [''];
            $this->tenures = ['base_price'];
        }
    }

    public function setBusinesses($businesses)
    {
        $this->product_businesses = $businesses;
    }

    public function setCategories($categories)
    {
        $this->product_category = $categories;
    }

    private function loadProductPrices($product)
    {
        $this->prices = [];
        $this->tenures = [];
        
        // Check if product has a prices relationship
        if (isset($product->prices) && count($product->prices) > 0) {
            foreach ($product->prices as $price) {
                $this->prices[] = $price->price;
                $this->tenures[] = $price->price_type;
            }
        } else {
            // Fall back to direct properties if available
            if ($product->base_price !== null) {
                $this->prices[] = $product->base_price;
                $this->tenures[] = 'base_price';
            }
            
            if ($product->standard_price !== null) {
                $this->prices[] = $product->standard_price;
                $this->tenures[] = 'standard_price';
            }
            
            if ($product->pro_price !== null) {
                $this->prices[] = $product->pro_price;
                $this->tenures[] = 'pro_price';
            }
        }
        
        // If no prices were loaded, initialize with one empty row
        if (empty($this->prices)) {
            $this->prices = [''];
            $this->tenures = ['base_price'];
        }
    }

    public function addPriceRow()
    {
        $this->prices[] = '';
        $this->tenures[] = '';
    }

    public function removePriceRow($index)
    {
        unset($this->prices[$index]);
        unset($this->tenures[$index]);
        
        // Re-index arrays
        $this->prices = array_values($this->prices);
        $this->tenures = array_values($this->tenures);
    }

    public function validateBasePriceExists()
    {
        return in_array('base_price', $this->tenures);
    }

    public function updatingProductIcon()
    {
        $this->isUploadingIcon = true;
    }

    public function updatedProductIcon()
    {
        $this->isUploadingIcon = false;
    }

    public function updatingProductImage()
    {
        $this->isUploadingImage = true;
    }

    public function updatedProductImage()
    {
        $this->isUploadingImage = false;
    }

    public function save()
    {
        $this->isProcessing = true;
        $this->validate();
        
        // Check if we have at least one base price
        if (!$this->validateBasePriceExists()) {
            $this->addError('basePriceError', 'Please include at least one Base Price.');
            $this->isProcessing = false;
            return;
        }
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            $productData = [
                'product_link' => $this->product_link,
                'lang_code' => $this->lang_code,
            ];
            
            // Set prices
            $productData['base_price'] = null;
            $productData['standard_price'] = null;
            $productData['pro_price'] = null;
            
            foreach ($this->prices as $index => $price) {
                if (isset($this->tenures[$index]) && !empty($this->tenures[$index])) {
                    $tenure = $this->tenures[$index];
                    $productData[$tenure] = $price;
                }
            }
            
            // Handle file uploads
            if ($this->product_icon) {
                $iconPath = $this->product_icon->store('products/icons', 'public');
                $productData['product_icon_path'] = 'storage/' . $iconPath;
                $productData['product_icon'] = 'storage/' . $iconPath; // For compatibility with older code
                
                // Delete old file if updating
                if ($this->isEditing && $this->existingIcon) {
                    $oldPath = str_replace('storage/', '', $this->existingIcon);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            } elseif ($this->isEditing) {
                $productData['product_icon_path'] = $this->existingIcon;
                $productData['product_icon'] = $this->existingIcon; // For compatibility with older code
            }
            
            if ($this->product_image) {
                $imagePath = $this->product_image->store('products/images', 'public');
                $productData['product_image_path'] = 'storage/' . $imagePath;
                $productData['product_image'] = 'storage/' . $imagePath; // For compatibility with older code
                
                // Delete old file if updating
                if ($this->isEditing && $this->existingImage) {
                    $oldPath = str_replace('storage/', '', $this->existingImage);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            } elseif ($this->isEditing) {
                $productData['product_image_path'] = $this->existingImage;
                $productData['product_image'] = $this->existingImage; // For compatibility with older code
            }
            
            // Create or update product
            if ($this->isEditing) {
                $this->product->update($productData);
                $product = $this->product;
            } else {
                $product = Product::create($productData);
            }
            
            // Update translation
            $product->translations()->updateOrCreate(
                ['lang_code' => $this->lang_code],
                [
                    'name' => $this->name,
                    'overview' => $this->overview,
                    'description' => $this->description,
                ]
            );
            
            // Sync relationships
            $product->businesses()->sync($this->product_businesses);
            $product->categories()->sync($this->product_category);
            
            DB::commit();
            
            session()->flash('success', $this->isEditing ? 'Product updated successfully!' : 'Product added successfully!');
            
            if (!$this->isEditing) {
                // Reset form after successful addition
                $this->reset(['name', 'product_link', 'overview', 'description', 
                             'product_businesses', 'product_category', 'product_icon', 'product_image']);
                $this->prices = [''];
                $this->tenures = ['base_price'];
            }
            
            return redirect()->route('products.index');
            
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
        
        $this->isProcessing = false;
    }

    public function render()
    {
        return view('livewire.product-form', [
            'businesses' => Business::with('translations')->get(),
            'categories' => Category::with('translations')->get(),
        ]);
    }
}