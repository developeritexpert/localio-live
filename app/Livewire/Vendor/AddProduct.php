<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Currency;
use Livewire\WithFileUploads;

use App\Models\ProductChangeRequest;
use App\Models\Product;

use App\Services\MediaService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AddProduct extends Component
{
    use WithFileUploads;

    public $product_name, $product_link;
    public $price, $selected_currency, $time_unit, $additional_info;
    public $renewal_price, $renewal_time_unit;
    public $discount_price, $discount_time_unit, $discount_expiration_date;

    public $product_icon, $product_image;
    public $lang_id, $currencies = [];

    public $productId;


    public function mount($productId = null)
    {
        $this->lang_id = getCurrentLanguageID();
        $this->currencies = Currency::pluck('code', 'symbol')->toArray();
    
        if ($productId) {
            $this->productId = $productId;
    
            $product = Product::with(['translations', 'prices'])->findOrFail($productId);
            
            // dd($product);
            $this->product_name = $product->translations?->name ?? '';

            // dd($this->product_name);

            $this->product_link = $product->translations?->product_link ?? '';
            // $this->is_affiliate = $product->translations?->is_affiliate ?? false;
    
            $price = $product->prices->first();
            if ($price) {
                $this->price = $price->price;
                $this->selected_currency = $price->currency;
                $this->time_unit = $price->time_unit;
                $this->additional_info = $price->additional_info;
                $this->discount_price = $price->discount_price;
                $this->discount_time_unit = $price->discount_time_units;
                $this->discount_expiration_date = $price->discount_expiration_date;
                $this->renewal_price = $price->renewal_price;
                $this->renewal_time_unit = $price->renewal_time_units;
            }
        }
    }


    public function loadProduct()
{
    if ($this->productId) {
        $product = Product::with(['translations', 'prices'])->findOrFail($this->productId);

        $this->product_name = $product->translations?->name ?? '';
        $this->product_link = $product->translations?->product_link ?? '';
        // $this->is_affiliate = $product->translations?->is_affiliate ?? false;

        $price = $product->prices->first();
        if ($price) {
            $this->price = $price->price;
            $this->selected_currency = $price->currency;
            $this->time_unit = $price->time_unit;
            $this->additional_info = $price->additional_info;
            $this->discount_price = $price->discount_price;
            $this->discount_time_unit = $price->discount_time_units;
            $this->discount_expiration_date = $price->discount_expiration_date;
            $this->renewal_price = $price->renewal_price;
            $this->renewal_time_unit = $price->renewal_time_units;
        }
    }
}



    public function submitForApproval()
    {
        $this->validate([
            'product_name' => 'required|string|max:255',
            'product_link' => 'required|url',
            'price' => 'required|numeric',
            'selected_currency' => 'required|string|max:5',
            'time_unit' => 'required|string',
        ]);
    
        $data = [
            'name' => $this->product_name,
            'link' => $this->product_link,
            // 'is_affiliate' => $this->is_affiliate,
            'lang_id' => $this->lang_id,
            'price' => [
                'price' => $this->price,
                'currency' => $this->selected_currency,
                'time_unit' => $this->time_unit,
                'additional_info' => $this->additional_info,
                'renewal_price' => $this->renewal_price,
                'renewal_time_unit' => $this->renewal_time_unit,
                'discount_price' => $this->discount_price,
                'discount_time_unit' => $this->discount_time_unit,
                'discount_expiration_date' => $this->discount_expiration_date,
            ],
        ];
    
        if ($this->productId) {
            $data['product_id'] = $this->productId;
        }
    
        ProductChangeRequest::create([
            'user_id' => auth()->id(),
            'field' => $this->productId ? 'edit_product' : 'new_product',
            'type' => 'product',
            'value' => json_encode($data),
            'lang_id' => $this->lang_id,
            'status' => 'pending',
        ]);
    
        $this->dispatch('swal:toast', [
            'type' => 'success',
            'message' => $this->productId
                ? 'Product update request sent for admin approval.'
                : 'Product submission sent for admin approval.',
        ]);
    
        if ($this->productId) {
            // Reload current data from DB
            $this->loadProduct();
        } else {
            // Reset form for new product
            $this->resetExcept(['currencies', 'lang_id']);
        }
        
    }
    
    


    public function render()
    {
        return view('livewire.vendor.add-product');
    }
}
