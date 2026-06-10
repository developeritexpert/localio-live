<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\Language;
use App\Models\ProCons;
use App\Models\ProConsTranslation;
use App\Models\ProductTranslation;
use App\Models\FeatureTransalte;
use App\Models\Feature;
use App\Models\{Business, Country, Currency, Filter, FilterOption, Media};
use App\Models\Price;
use App\Models\ProductFeature;
use Illuminate\Support\Facades\DB;
use App\Services\MediaService;
use App\Models\ProductFilterOption;
use App\Http\Requests\ProductRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminProductController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function products()
    {
        $lang_id = getCurrentLanguageID();
        $products = Product::with(['categories', 'countries','businesses.translations'=>function($query) use ($lang_id){
            $query->where('lang_id',$lang_id);
        }])->where('lang_id', $lang_id)
            ->latest()
            ->get();
        $currencies = Currency::all();
        return view('Admin.products.index', compact('products', 'currencies'));
    }
    public function getBusinessCategory(Request $request)
    {
        $lang_id = getCurrentLanguageID();
        $businessId = $request->get('business_id');
        $business = Business::with(['category.categoryTranslations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])->find($businessId);

        if ($business && $business->category_id) {
            return response()->json([
                'success' => true,
                'category_id' => $business->category_id,
                'category_name' => $business->category->categoryTranslations->first()->name ?? 'Unknown Category'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No category found for this business'
        ]);
    }

    public function getCategoryName(Request $request)
    {
        $lang_id = getCurrentLanguageID();
        $categoryId = $request->get('category_id');

        $category = Category::with(['categoryTranslations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])->find($categoryId);

        if ($category && $category->categoryTranslations->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'category_name' => $category->categoryTranslations->first()->name
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Category not found'
        ]);
    }
    // Add the new product from the admin panel
    public function productAdd()
    {

        $lang_id = getCurrentLanguageID();
        $businesses = Business::with(['translations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])->where('status',1)->whereHas('translations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })->where('status', '1')->get();
        $categories = Category::with(['categoryTranslations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])->whereHas('categoryTranslations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })->get();
        $currencies = Currency::all();
        // Get all active countries
        $countries = Country::where('status', true)->orderBy('name')->get();
        $is_affiliate=1;
        return view('Admin.products.add_product', compact('is_affiliate','categories', 'currencies', 'businesses', 'countries'));
    }
    public function productAddProccess(ProductRequest $request)
    {
        // dd($request->all());
        $validatedData = $request->validated();
        $lang_id = getCurrentLanguageID();
        DB::beginTransaction();
        try {
            // Additional server-side validation for pricing logic
            $this->validatePricingLogic($request);
            // dd($request->all());
            // Create Product and assign base fields
            $product = new Product();
            $product->product_link = $request->product_link;
            $product->status = $request->status;
            // dd($request->all());
            // Handle country availability
            if ($request->has('product_countries')) {
                $product->active_all_countries = 0;
            } else {
                $product->active_all_countries = 1;
            }
            // dd($request->all());
            // Upload Product Icon with validation
            if ($request->hasFile('product_icon')) {
                $this->validateImageFile($request->file('product_icon'), 'product_icon');
                $media = $this->mediaService->uploadMedia($request->file('product_icon'), 'products/icons');
                $product->product_icon = $media->id ?? null;
            }

            // Upload Product Image with validation
            if ($request->hasFile('product_image')) {
                $this->validateImageFile($request->file('product_image'), 'product_image');

                $media = $this->mediaService->uploadMedia($request->file('product_image'), 'products/images');

                $product->product_image = $media->id ?? null;
            }

            $product->lang_id = $lang_id;
            $product->save();

            // Handle dynamic filters with validation
            $this->handleProductFilters($request, $product);

            // Attach relationships
            $this->attachProductRelationships($request, $product);

            // Handle pricing with comprehensive validation
            $this->handleProductPricing($request, $product);

            // Handle Product Translation
            $this->createProductTranslation($request, $product, $lang_id);

            DB::commit();

            return redirect()->route('products')
                ->with('success', 'Product added successfully with all pricing configurations.');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Product creation failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->except(['product_icon', 'product_image'])
            ]);

            return back()
                ->with('error', 'An error occurred while creating the product. Please try again.')
                ->withInput();
        }
    }
    /**
     * Validate pricing logic with business rules
     */
    private function validatePricingLogic($request)
    {
        $price = (float) $request->prices;
        $discountPrice = $request->discount_prices ? (float) $request->discount_prices : null;
        $renewalPrice = $request->renewal_prices ? (float) $request->renewal_prices : null;
        $timeUnit = $request->time_units;

        $errors = [];

        // Validate discount price
        if ($discountPrice !== null) {
            if ($discountPrice >= $price) {
                $errors['discount_prices'] = 'Discount price must be less than the regular price.';
            }

            if (!$request->discount_expiration_dates) {
                $errors['discount_expiration_dates'] = 'Expiration date is required when discount price is provided.';
            }

            // Validate discount percentage isn't too high (e.g., more than 90% off)
            $discountPercent = (($price - $discountPrice) / $price) * 100;
            if ($discountPercent > 90) {
                $errors['discount_prices'] = 'Discount cannot exceed 90% of the regular price.';
            }
        }

        // Validate renewal price logic
        if ($renewalPrice !== null) {
            if ($timeUnit === 'one_time') {
                $errors['renewal_prices'] = 'One-time products cannot have renewal prices.';
            }

            if (!$request->renewal_time_units) {
                $errors['renewal_time_units'] = 'Renewal time unit is required when renewal price is provided.';
            }

            // Warn if renewal is significantly different from regular price
            if ($renewalPrice > ($price * 2)) {
                $errors['renewal_prices'] = 'Renewal price seems unusually high. Please verify the amount.';
            }
        }

        // Validate expiration date
        if ($request->discount_expiration_dates) {
            $expirationDate = \Carbon\Carbon::parse($request->discount_expiration_dates);
            $today = \Carbon\Carbon::now();

            if ($expirationDate->lte($today)) {
                $errors['discount_expiration_dates'] = 'Discount expiration date must be in the future.';
            }

            if ($expirationDate->gt($today->copy()->addYears(2))) {
                $errors['discount_expiration_dates'] = 'Discount expiration date cannot be more than 2 years in the future.';
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Validate uploaded image files
     */
    private function validateImageFile($file, $fieldName)
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw ValidationException::withMessages([
                $fieldName => 'File must be an image (jpeg, png, jpg, or gif).'
            ]);
        }

        if ($file->getSize() > $maxSize) {
            throw ValidationException::withMessages([
                $fieldName => 'File size must not exceed 2MB.'
            ]);
        }
    }

    /**
     * Handle product filters with validation
     */
    private function handleProductFilters($request, $product)
    {
        if ($request->filled('filters') && $request->filled('product_category')) {
            foreach ($request->filters as $filterId => $optionIds) {
                if (is_array($optionIds)) {
                    foreach ($optionIds as $filterOptionId) {
                        // Validate that the filter option belongs to the filter
                        $validOption = DB::table('filter_options')
                            ->where('id', $filterOptionId)
                            ->where('filter_id', $filterId)
                            ->exists();

                        if (!$validOption) {
                            throw new Exception("Invalid filter option selected for filter ID: {$filterId}");
                        }

                        DB::table('product_filter_options')->insert([
                            'category_id' => $request->product_category,
                            'filter_id' => $filterId,
                            'filter_option_id' => $filterOptionId,
                            'product_id' => $product->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Attach product relationships
     */
    private function attachProductRelationships($request, $product)
    {
        // Attach businesses
        if ($request->has('product_businesses')) {
            $businessIds = array_filter($request->product_businesses);
            if (!empty($businessIds)) {
                $product->businesses()->sync($businessIds);
            }
        }
        // Attach categories
        if ($request->has('product_category')) {
            $product->categories()->sync([$request->product_category]);
        }
        // Attach countries
        if ($request->has('product_countries')) {
            $countryIds = array_filter($request->product_countries);
            if (!empty($countryIds)) {
                $product->countries()->sync($countryIds);
            }
        }
    }

    /**
     * Handle product pricing with validation
     */
    private function handleProductPricing($request, $product)
    {
        if (!$request->filled('prices') || !$request->filled('currencies')) {
            throw new Exception('Price and currency are required.');
        }

        // Prepare pricing data
        $pricingData = [
            'product_id' => $product->id,
            'price' => (float) $request->prices,
            'currency' => $request->currencies,
            'time_unit' => $request->time_units,
            'additional_info' => $request->price_descriptions,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Add discount information if provided
        if ($request->filled('discount_prices')) {
            $pricingData['discount_price'] = (float) $request->discount_prices;
            $pricingData['discount_time_units'] = $request->discount_time_units;
            $pricingData['discount_expiration_date'] = $request->discount_expiration_dates;
        }

        // Add renewal information if provided
        if ($request->filled('renewal_prices')) {
            $pricingData['renewal_price'] = (float) $request->renewal_prices;
            $pricingData['renewal_time_units'] = $request->renewal_time_units;
        }

        // Insert pricing data
        $product->prices()->insert([$pricingData]);
    }

    /**
     * Create product translation
     */
    private function createProductTranslation($request, $product, $lang_id)
    {
        $translationData = [
            'product_id' => $product->id,
            'name' => trim($request->name),
            'slug' => Str::slug($request->name),
            'product_link' => $request->product_link,
            'lang_id' => $lang_id,
        ];

        // Add optional fields if they exist
        if ($request->filled('description')) {
            $translationData['description'] = $request->description;
        }

        if ($request->filled('overview')) {
            $translationData['overview'] = $request->overview;
        }

        $product->translation()->create($translationData);
    }
    protected function getProductFilters($productId)
    {
        $productFilters = ProductFilterOption::where('product_id', $productId)
            ->select('filter_id', 'filter_option_id')
            ->get();

        // Group options by filter
        $groupedFilters = [];
        foreach ($productFilters as $filter) {
            if (!isset($groupedFilters[$filter->filter_id])) {
                $groupedFilters[$filter->filter_id] = [
                    'filter_id' => $filter->filter_id,
                    'options' => []
                ];
            }

            $groupedFilters[$filter->filter_id]['options'][] = [
                'id' => $filter->filter_option_id
            ];
        }

        return array_values($groupedFilters);
    }
    public function fetchFilters(Request $request)
    {
        $categoryIds = [$request->categories]; // Ensure it's an array
        $lang_id = getCurrentLanguageID();

        $filters = Filter::with([
            'options.translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id)
                    ->select('id', 'filter_option_id', 'name');
            },
            'translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id)
                    ->select('id', 'filter_id', 'name');
            }
        ])
            ->whereIn('category_id', $categoryIds) // Expects an array
            ->select('id', 'category_id')
            ->get();
        if ($filters->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No filters available for selected category']);
        }

        return response()->json(['success' => true, 'filters' => $filters]);
    }

    public function productEdit($id)
    {
        $lang_id = getCurrentLanguageID();
        // Fetch Product with Translations
        $product = Product::with([
            'translation' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'filterOptions.filter.translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'filterOptions.filterOption.translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'countries',
            'prices'
        ])->find($id);
        if (!$product) {
            abort(404, 'Product not found');
        }
        $currencies = Currency::all();
        // Get all active countries
        $countries = Country::where('status', true)->orderBy('name')->get();

        // Get product's selected country IDs
        $selectedCountries = $product->countries->pluck('id')->toArray();

        // Fetch Categories Related to Product
        $product_category = Category::with(['categoryTranslations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])->whereHas('products', function ($query) use ($id) {
            $query->where('product_id', $id)->where('category_products.status', 'active');
        })->get();
        // Fetch Businesses with Active Status
        $product_business = Business::with(['translations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])->whereHas('products', function ($query) use ($id) {
            $query->where('product_id', $id)->where('business_product.status', 'active');
        })->where('status', '1')->get();

        $businesses = Business::with(['translations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])->whereHas('translations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })->where('status', '1')->get();
        $categories = Category::with(['categoryTranslations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])->whereHas('categoryTranslations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })->get();
        // Format the selected filters to pass to JavaScript
        $selectedFilters = [];

        // Group by filter_id
        $groupedFilterOptions = $product->filterOptions->groupBy('filter_id');

        foreach ($groupedFilterOptions as $filterId => $options) {
            $filterOptions = [];
            foreach ($options as $option) {
                $filterOptions[] = [
                    'id' => $option->filterOption->id,
                    'name' => $option->filterOption->translations->first() ?
                        $option->filterOption->translations->first()->name :
                        'Option #' . $option->filterOption->id
                ];
            }

            $selectedFilters[] = [
                'filter_id' => $filterId,
                'options' => $filterOptions
            ];
        }

        return view('Admin.products.update_product', compact('selectedFilters', 'currencies', 'product', 'product_category', 'product_business', 'businesses', 'categories', 'countries', 'selectedCountries'));
    }
    private function validatePriceData($request)
    {
        $regularPrice = (float) $request->input('prices', 0);
        $discountPrice = $request->input('discount_prices') ? (float) $request->input('discount_prices') : null;
        $renewalPrice = $request->input('renewal_prices') ? (float) $request->input('renewal_prices') : null;
        $discountExpiration = $request->input('discount_expiration_dates');

        $errors = [];

        // Validate regular price
        if ($regularPrice <= 0) {
            $errors['prices'] = ['Regular price must be greater than 0'];
        }

        // Validate discount price logic
        if ($discountPrice !== null) {
            if ($discountPrice <= 0) {
                $errors['discount_prices'] = ['Discount price must be greater than 0'];
            } elseif ($discountPrice >= $regularPrice) {
                $errors['discount_prices'] = ['Discount price must be less than regular price'];
            } else {
                // Check discount percentage
                $discountPercentage = (($regularPrice - $discountPrice) / $regularPrice) * 100;
                if ($discountPercentage < 1) {
                    $errors['discount_prices'] = ['Discount should provide at least 1% savings'];
                } elseif ($discountPercentage > 99) {
                    $errors['discount_prices'] = ['Discount cannot be more than 99% off the regular price'];
                }
            }

            // Validate discount expiration
            if (!$discountExpiration) {
                $errors['discount_expiration_dates'] = ['Expiration date is required when discount price is provided'];
            } else {
                $expDate = Carbon::parse($discountExpiration);
                $now = Carbon::now();

                if ($expDate->isPast()) {
                    $errors['discount_expiration_dates'] = ['Expiration date must be in the future'];
                }  elseif ($expDate->diffInDays($now) > 730) {
                    $errors['discount_expiration_dates'] = ['Expiration date should not be more than 2 years from now'];
                }
            }

            // Validate discount time unit
            if (!$request->input('discount_time_units')) {
                $errors['discount_time_units'] = ['Time unit is required when discount price is provided'];
            }
        }

        // Validate renewal price logic
        if ($renewalPrice !== null) {
            if ($renewalPrice <= 0) {
                $errors['renewal_prices'] = ['Renewal price must be greater than 0'];
            }

            if (!$request->input('renewal_time_units')) {
                $errors['renewal_time_units'] = ['Time unit is required when renewal price is provided'];
            }
        }

        // Throw validation exception if there are errors
        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Update product prices with validation
     */
    private function updateProductPrices($product, $request)
    {
        // Validate required price fields
        if (!$request->filled('prices') || !$request->filled('currencies')) {
            throw ValidationException::withMessages([
                'prices' => ['Price is required'],
                'currencies' => ['Currency is required']
            ]);
        }

        // Delete existing prices to avoid duplicates
        $product->prices()->delete();

        // Prepare price data
        $priceData = [
            'product_id' => $product->id,
            'price' => (float) $request->input('prices'),
            'currency' => $request->input('currencies'),
            'time_unit' => $request->input('time_units', 'month'),
            'additional_info' => $request->input('price_descriptions'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Add discount fields if provided
        if ($request->filled('discount_prices')) {
            $priceData['discount_price'] = (float) $request->input('discount_prices');
            $priceData['discount_time_units'] = $request->input('discount_time_units');
            $priceData['discount_expiration_date'] = $request->input('discount_expiration_dates');
        }

        // Add renewal fields if provided
        if ($request->filled('renewal_prices')) {
            $priceData['renewal_price'] = (float) $request->input('renewal_prices');
            $priceData['renewal_time_units'] = $request->input('renewal_time_units');
        }
        // Insert price data
        try {
            $priceData['product_id'] = $product->id;
            $priceData['created_at'] = now();
            $priceData['updated_at'] = now();
            ProductPrice::insert([$priceData]);
        } catch (Exception $e) {
            Log::error('Failed to insert price data', [
                'product_id' => $product->id,
                'price_data' => $priceData,
                'error' => $e->getMessage()
            ]);

            throw new Exception('Failed to save price information' . $e->getMessage());
        }
    }
    public function productUpdateProccess(ProductRequest $request, $id)
    {

        // dd($request->all());
         $validatedData = $request->validated();
        $lang_id = getCurrentLanguageID();

        // Find the product or abort
        $product = Product::find($id);
        if (!$product) {
            abort(404, 'Product not found');
        }
        // Additional server-side validation for price logic
        $this->validatePriceData($request);
        DB::beginTransaction();
        try {
            // Update basic product information
            $product->product_link = $request->product_link;
            $product->status = $request->status;
            $product->lang_id = $lang_id;

            if ($request->has('product_countries')) {
                $product->active_all_countries = 0;
            } else {
                $product->active_all_countries = 1;
            }

            // Upload Product Icon
            if ($request->hasFile('product_icon')) {
                // Delete old icon if exists
                if ($product->product_icon) {
                    $this->mediaService->deleteMedia($product->product_icon);
                }
                $media = $this->mediaService->uploadMedia($request->file('product_icon'), 'products/images');
                $product->product_icon = $media->id ?? null;
            }

            // Upload Product Image
            if ($request->hasFile('product_image')) {
                // Delete old image if exists
                if ($product->product_image) {
                    $this->mediaService->deleteMedia($product->product_image);
                }
                $media = $this->mediaService->uploadMedia($request->file('product_image'), 'products/images');
                $product->product_image = $media->id ?? null;
            }

            $product->save();
            // dd($request->all());
            // Sync businesses
            if ($request->has('product_businesses')) {
                $product->businesses()->sync($request->product_businesses);
            } else {
                $product->businesses()->detach();
            }

            // Sync categories
            if ($request->has('product_category')) {
                $product->categories()->sync([$request->product_category]);
            } else {
                $product->categories()->detach();
            }

            // Sync countries
            if ($request->has('product_countries')) {
                $product->countries()->sync($request->product_countries);
            } else {
                $product->countries()->detach();
            }
            // Handle filter options
            if ($request->filled('product_category')) {
                ProductFilterOption::where('product_id', $product->id)->delete();

                if ($request->filled('filters')) {
                    foreach ($request->filters as $filterId => $optionIds) {
                        if (is_array($optionIds)) {
                            foreach ($optionIds as $filterOptionId) {
                                DB::table('product_filter_options')->insert([
                                    'category_id' => $request->product_category,
                                    'filter_id' => $filterId,
                                    'filter_option_id' => $filterOptionId,
                                    'product_id' => $product->id,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
                        }
                    }
                }
            }

            // Handle prices with enhanced validation
            $this->updateProductPrices($product, $request);

            // Handle Product Translation
            $product->translation()->updateOrCreate([
                'product_id' => $product->id,
                'lang_id' => $lang_id,
            ], [
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description ?? null,
                'overview' => $request->overview ?? null,
                'product_link' => $request->product_link,
            ]);

            // Commit the transaction
            DB::commit();

            return redirect()->route('products')->with('success', 'Product updated successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product update failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'An error occurred while updating the product. Please try again.' . $e->getMessage());
            return back()->withInput();
        }
    }
    public function removeProduct($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return redirect()
                ->back()
                ->with('error', 'product not found');
        }
        $product->delete();
        $product->businesses()->detach();
        ProductTranslation::where('product_id', $id)->delete();
        CategoryProduct::where('product_id', $id)->delete();
        ProductPrice::where('product_id', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'product remove successfully');
    }
    public function deletePrice($id)
    {
        $price = Price::find($id);
        if ($price) {
            $price->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
