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
         'name' => 'required|unique:product_translations,name,' . $this->input('product_id') . ',product_id',
            'product_link' => 'required|url|max:500',
            'status' => 'required|in:public,private',
            'product_countries' => 'nullable|array',
            'product_countries.*' => 'exists:countries,id',
            'product_businesses' => 'required|array|min:1',
            'product_businesses.*' => 'exists:businesses,id',
            'product_category' => ['required', 'exists:categories,id', function($attribute, $value, $fail) {
                if (\App\Models\Category::where('id', $value)->whereNull('parent_id')->exists()) {
                    $fail('The selected category must be a sub-category.');
                }
            }],
            'product_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Price validations
            'prices' => 'required|numeric|min:0',
            'currencies' => 'required|string|exists:currencies,symbol',
            'time_units' => 'required|in:one_time,day,week,month,quarter,year',
            'price_descriptions' => 'nullable|string|max:255',

            // Discount price validations
            'discount_prices' => 'nullable|numeric|min:0|lt:prices',
            'discount_time_units' => 'nullable|in:one_time,day,week,month,quarter,year',
            'discount_expiration_dates' => 'required_with:discount_prices|nullable|date|after:today',

            // Renewal price validations
            'renewal_prices' => 'nullable|numeric|min:0',
            'renewal_time_units' => 'nullable|in:one_time,day,week,month,quarter,year|required_with:renewal_prices',

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

            // Discount messages
            'discount_prices.numeric' => 'Discount price must be a valid number.',
            'discount_prices.min' => 'Discount price cannot be negative.',
            'discount_prices.lt' => 'Discount price must be less than the regular price.',
            'discount_expiration_dates.required_with' => 'Discount expiration date is required when discount price is provided.',
            'discount_expiration_dates.date' => 'Discount expiration date must be a valid date.',
            'discount_expiration_dates.after' => 'Discount expiration date must be a future date.',

            // Renewal messages
            'renewal_prices.numeric' => 'Renewal price must be a valid number.',
            'renewal_prices.min' => 'Renewal price cannot be negative.',
            'renewal_time_units.required_with' => 'Renewal time unit is required when renewal price is provided.',
            'renewal_time_units.in' => 'Selected renewal time unit is invalid.',
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional custom validations

            // Validate discount price against regular price
            if ($this->filled('discount_prices') && $this->filled('prices')) {
                if ((float)$this->discount_prices >= (float)$this->prices) {
                    $validator->errors()->add('discount_prices', 'Discount price must be less than the regular price.');
                }
            }

            // Validate renewal price logic (optional: renewal shouldn't be higher than regular price)
            if ($this->filled('renewal_prices') && $this->filled('prices')) {
                if ((float)$this->renewal_prices > (float)$this->prices * 1.5) { // Allow 50% higher for renewal
                    $validator->errors()->add('renewal_prices', 'Renewal price seems unusually high compared to regular price.');
                }
            }

            // Validate that one-time products don't have renewal prices
            if ($this->time_units === 'one_time' && $this->filled('renewal_prices')) {
                $validator->errors()->add('renewal_prices', 'One-time products cannot have renewal prices.');
            }

            // Validate discount expiration date is reasonable (not too far in future)
            if ($this->filled('discount_expiration_dates')) {
                $expirationDate = \Carbon\Carbon::parse($this->discount_expiration_dates);
                $maxDate = \Carbon\Carbon::now()->addYears(2); // Max 2 years in future

                if ($expirationDate->gt($maxDate)) {
                    $validator->errors()->add('discount_expiration_dates', 'Discount expiration date cannot be more than 2 years in the future.');
                }
            }
        });
    }
}
