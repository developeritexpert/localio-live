<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'product_link' => 'nullable|url|max:500',
            'status' => 'required|in:public,private',
            'active_all_countries' => 'nullable|in:0,1',
            'product_countries' => 'nullable|array',
            'product_countries.*' => 'exists:countries,id',
            'active_all_subcategories' => 'nullable|in:0,1',
            'product_subcategories' => 'nullable|array',
            'product_subcategories.*' => 'exists:categories,id',
            'product_businesses' => 'nullable|array',
            'product_businesses.*' => 'exists:businesses,id',
            'product_category' => 'nullable|exists:categories,id',
            'product_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Price validations
            'prices' => 'required|numeric|min:0',
            'currencies' => 'required|string|exists:currencies,symbol',
            'time_units' => 'required|in:one_time,day,week,month,quarter,year',
            'price_descriptions' => 'nullable|string|max:255',

            // Offer / Pricing Options
            'pricing_options' => 'nullable|array',
            'pricing_options.*' => 'exists:pricing_options,id',

            // Filter validations
            'filters' => 'nullable|array',
            'filters.*' => 'array',
            'filters.*.*' => 'exists:filter_options,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'product_link.required' => 'Product link is required.',
            'product_link.url' => 'Product link must be a valid URL.',
            'product_businesses.required' => 'At least one business must be selected.',
            'product_businesses.min' => 'At least one business must be selected.',
            'product_category.required' => 'Product category is required.',
            'product_icon.image' => 'Product icon must be an image file.',
            'product_icon.mimes' => 'Product icon must be jpeg, png, jpg, or gif format.',
            'product_icon.max' => 'Product icon must not exceed 2MB.',
            'product_image.image' => 'Product image must be an image file.',
            'product_image.mimes' => 'Product image must be jpeg, png, jpg, or gif format.',
            'product_image.max' => 'Product image must not exceed 2MB.',

            // Price messages
            'prices.required' => 'Price is required.',
            'prices.numeric' => 'Price must be a valid number.',
            'prices.min' => 'Price cannot be negative.',
            'currencies.required' => 'Currency is required.',
            'currencies.exists' => 'Selected currency is invalid.',
            'time_units.required' => 'Time unit is required.',
            'time_units.in' => 'Selected time unit is invalid.',
        ];
    }
    protected function prepareForValidation()
    {
        // This will make product_id available in the rules()
        if ($this->route('id')) {
            $this->merge([
                'product_id' => $this->route('id'),
            ]);
        }
    }
}
