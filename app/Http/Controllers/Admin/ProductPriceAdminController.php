<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\BusinessOfferProduct;
use App\Models\BusinessOfferPlan;
use App\Models\BusinessOfferPrice;
use App\Models\BusinessOfferPlanFeature;
use App\Models\BusinessOfferSpecification;
use App\Models\BusinessFaq;
use App\Models\BusinessFaqCategory;
use App\Models\BusinessFaqTranslation;
use App\Models\Feature;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductPriceAdminController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $search = trim($request->get('search', ''));

        $counts = [
            'all' => Business::count(),
            'with_products' => Business::whereHas('offerProducts')->count(),
            'without_products' => Business::whereDoesntHave('offerProducts')->count(),
        ];

        $query = Business::with([
            'translations' => fn($q) => $q->where('lang_id', getCurrentLanguageID()),
            'category.translations' => fn($q) => $q->where('lang_id', getCurrentLanguageID()),
            'offerProducts.plans.prices',
            'offerProducts.directPrices',
            'faqCategories' => fn($q) => $q->where(function($cq) {
                $cq->where('name', 'like', '%pric%')
                   ->orWhere('name', 'like', '%plan%')
                   ->orWhere('name', 'like', '%product%');
            })->withCount('faqs'),
        ])->withCount(['offerProducts']);

        if ($tab === 'with_products') {
            $query->whereHas('offerProducts');
        } elseif ($tab === 'without_products') {
            $query->whereDoesntHave('offerProducts');
        }

        if (!empty($search)) {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $businesses = $query->orderByRaw('COALESCE(pricing_updated_at, updated_at) DESC')
            ->paginate(15)
            ->withQueryString();

        return view('Admin.product_prices.index', compact('businesses', 'tab', 'search', 'counts'));
    }

    public function manage($businessId)
    {
        $business = Business::with([
            'translations' => fn($q) => $q->where('lang_id', getCurrentLanguageID()),
            'offerProducts' => fn($q) => $q->with(['plans.prices', 'plans.planFeatures', 'plans.specifications', 'directPrices', 'directFeatures']),
        ])->findOrFail($businessId);

        $langId = getCurrentLanguageID();
        $features = Feature::with(['translations' => fn($q) => $q->where('lang_id', $langId)])
            ->where('status', 'active')
            ->get()
            ->map(function ($f) use ($langId) {
                $trans = $f->translations->first();
                return [
                    'id' => $f->id,
                    'name' => $trans->name ?? ('Feature #' . $f->id),
                ];
            });

        $countries = Country::where('status', 1)
            ->orderBy('id', 'asc')
            ->get(['id', 'name'])
            ->map(function ($c) {
                return [
                    'id' => (string)$c->id,
                    'name' => $c->name,
                ];
            });

        $totalProducts = $business->offerProducts->count();
        $totalPlans = 0;
        $totalPriceEntries = 0;
        $currencies = [];

        foreach ($business->offerProducts as $prod) {
            if ($prod->plans->isNotEmpty()) {
                $totalPlans += $prod->plans->count();
                foreach ($prod->plans as $plan) {
                    $totalPriceEntries += $plan->prices->count();
                    foreach ($plan->prices as $p) {
                        if ($p->currency) $currencies[$p->currency] = true;
                    }
                }
            } else {
                $totalPriceEntries += $prod->directPrices->count();
                foreach ($prod->directPrices as $p) {
                    if ($p->currency) $currencies[$p->currency] = true;
                }
            }
        }

        $stats = [
            'products_plans_count' => $totalProducts + $totalPlans,
            'price_entries_count' => $totalPriceEntries,
            'currencies_count' => count($currencies),
        ];

        return view('Admin.product_prices.manage', compact('business', 'features', 'stats', 'countries'));
    }

    public function storeProduct(Request $request, $businessId)
    {
        $business = Business::findOrFail($businessId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $productId = $request->input('product_id');
            $product = $productId ? BusinessOfferProduct::where('business_id', $businessId)->find($productId) : new BusinessOfferProduct();
            
            if (!$product) {
                $product = new BusinessOfferProduct();
            }

            $product->business_id = $businessId;
            $product->name = $request->input('name');
            $product->description = $request->input('description');
            $product->names_by_locale = $request->input('names_by_locale', []);
            $product->has_plans = $request->boolean('has_plans', true);
            $product->status = $request->input('status', 'public');
            $product->save();

            $plansData = $request->input('plans', []);
            $keepPlanIds = [];

            if ($product->has_plans && !empty($plansData)) {
                $product->directPrices()->delete();
                $product->directFeatures()->delete();

                foreach ($plansData as $idx => $pData) {
                    $planId = $pData['id'] ?? null;
                    $plan = $planId ? BusinessOfferPlan::where('business_product_id', $product->id)->find($planId) : new BusinessOfferPlan();
                    if (!$plan) {
                        $plan = new BusinessOfferPlan();
                    }

                    $plan->business_product_id = $product->id;
                    $plan->name = $pData['name'];
                    $plan->short_description = $pData['short_description'] ?? null;
                    $plan->names_by_locale = $pData['names_by_locale'] ?? [];
                    $plan->is_featured = !empty($pData['is_featured']);
                    $plan->sort_order = $idx;
                    $plan->status = 'public';
                    $plan->save();

                    $keepPlanIds[] = $plan->id;

                    $plan->prices()->delete();
                    $prices = $pData['prices'] ?? [];
                    foreach ($prices as $pr) {
                        $regPrice = (float)($pr['amount'] ?? ($pr['regular_price'] ?? 0));
                        $priceModel = ($regPrice == 0.0 && ($pr['term'] ?? '') !== 'custom') ? 'free' : ($pr['price_model'] ?? 'fixed');

                        BusinessOfferPrice::create([
                            'business_plan_id' => $plan->id,
                            'country_region' => $pr['market'] ?? ($pr['country_region'] ?? 'all'),
                            'currency' => strtoupper($pr['currency'] ?? 'USD'),
                            'billing_period' => $pr['term'] ?? ($pr['billing_period'] ?? '1m'),
                            'custom_unit' => $pr['customUnit'] ?? null,
                            'regular_price' => $regPrice,
                            'renewal_price' => (isset($pr['renewal_price']) && $pr['renewal_price'] !== '') ? (float)$pr['renewal_price'] : null,
                            'intro_price' => (isset($pr['intro']) && $pr['intro'] !== '') ? (float)$pr['intro'] : null,
                            'intro_duration' => $pr['duration'] ?? null,
                            'price_model' => $priceModel,
                            'is_active' => true,
                        ]);
                    }

                    $plan->planFeatures()->delete();
                    $featureIds = $pData['features'] ?? [];
                    $featuredHighlightIds = $pData['featured_feature_ids'] ?? [];
                    foreach ($featureIds as $fIdx => $fId) {
                        BusinessOfferPlanFeature::create([
                            'business_plan_id' => $plan->id,
                            'feature_id' => (int)$fId,
                            'is_featured_highlight' => in_array($fId, $featuredHighlightIds),
                            'sort_order' => $fIdx,
                        ]);
                    }

                    $plan->specifications()->delete();
                    $specs = $pData['specifications'] ?? [];
                    foreach ($specs as $sp) {
                        if (!empty($sp['code']) && isset($sp['value'])) {
                            BusinessOfferSpecification::create([
                                'business_plan_id' => $plan->id,
                                'code' => $sp['code'],
                                'value' => (string)$sp['value'],
                                'unit' => $sp['unit'] ?? null,
                            ]);
                        }
                    }
                }
                BusinessOfferPlan::where('business_product_id', $product->id)
                    ->whereNotIn('id', $keepPlanIds)
                    ->delete();
            } else {
                $product->directPrices()->delete();
                $directPrices = $request->input('prices', []);
                foreach ($directPrices as $pr) {
                    $regPrice = (float)($pr['amount'] ?? ($pr['regular_price'] ?? 0));
                    $priceModel = ($regPrice == 0.0) ? 'free' : ($pr['price_model'] ?? 'fixed');

                    BusinessOfferPrice::create([
                        'business_product_id' => $product->id,
                        'country_region' => $pr['market'] ?? ($pr['country_region'] ?? 'all'),
                        'currency' => strtoupper($pr['currency'] ?? 'USD'),
                        'billing_period' => 'once',
                        'unit' => $pr['unit'] ?? null,
                        'regular_price' => $regPrice,
                        'renewal_price' => null,
                        'price_model' => $priceModel,
                        'is_active' => true,
                    ]);
                }
            }

            $business->pricing_updated_at = now();
            $business->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Product / plan saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving product: ' . $e->getMessage()], 500);
        }
    }

    public function deleteProduct($businessId, $productId)
    {
        $business = Business::findOrFail($businessId);
        $product = BusinessOfferProduct::where('business_id', $businessId)->findOrFail($productId);
        $product->delete();

        $business->pricing_updated_at = now();
        $business->save();

        return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
    }

    public function batchImport(Request $request)
    {
        $jsonString = $request->input('json_content');
        if ($request->hasFile('json_file')) {
            $jsonString = file_get_contents($request->file('json_file')->getRealPath());
        }

        if (empty($jsonString)) {
            return back()->with('error', 'Please provide JSON content or upload a JSON file.');
        }

        $jsonString = preg_replace('/^\xEF\xBB\xBF/', '', trim($jsonString));

        $data = json_decode($jsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $lines = preg_split('/\r\n|\r|\n/', $jsonString);
            $parsedLines = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $decodedLine = json_decode($line, true);
                if ($decodedLine && is_array($decodedLine)) {
                    $parsedLines[] = $decodedLine;
                }
            }
            if (!empty($parsedLines)) {
                $data = $parsedLines;
            } else {
                return back()->with('error', 'Invalid JSON format: ' . json_last_error_msg());
            }
        }

        $businesses = [];
        if (isset($data['businesses']) && is_array($data['businesses'])) {
            $businesses = $data['businesses'];
        } elseif (isset($data['data']) && is_array($data['data'])) {
            $businesses = $data['data'];
        } elseif (is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                $businesses = $data;
            } elseif (isset($data['business_id']) || isset($data['id']) || isset($data['name']) || isset($data['products'])) {
                $businesses = [$data];
            }
        }

        if (empty($businesses)) {
            return back()->with('error', 'JSON does not contain any recognizable business records.');
        }

        DB::beginTransaction();
        try {
            $importedCount = 0;
            $importedProductsCount = 0;
            $importedPlansCount = 0;
            $importedFaqsCount = 0;
            $skippedBusinesses = [];

            foreach ($businesses as $bData) {
                if (!is_array($bData)) continue;

                $business = null;
                $businessId = $bData['business_id'] ?? ($bData['id'] ?? null);
                if ($businessId && is_numeric($businessId)) {
                    $business = Business::find($businessId);
                }

                if (!$business) {
                    $name = trim($bData['name'] ?? ($bData['business_name'] ?? ''));
                    if ($name !== '') {
                        $transBizId = BusinessTranslation::where('name', $name)->value('business_id');
                        if (!$transBizId) {
                            $transBizId = BusinessTranslation::where('name', 'like', '%' . $name . '%')->value('business_id');
                        }
                        if ($transBizId) {
                            $business = Business::find($transBizId);
                        }
                    }
                }

                if (!$business) {
                    $url = trim($bData['url'] ?? ($bData['website'] ?? ($bData['permanent_url'] ?? '')));
                    if ($url !== '') {
                        $host = parse_url($url, PHP_URL_HOST) ?: $url;
                        $hostClean = preg_replace('/^www\./i', '', strtolower($host));
                        $business = Business::where('permanent_url', 'like', '%' . $hostClean . '%')
                            ->orWhere('affiliate_link', 'like', '%' . $hostClean . '%')
                            ->first();
                    }
                }

                if (!$business) {
                    $label = $bData['name'] ?? ($bData['business_name'] ?? ($bData['business_id'] ?? ($bData['id'] ?? 'Unknown')));
                    $skippedBusinesses[] = "#" . $label;
                    continue;
                }

                $products = $bData['products'] ?? [];
                if (is_array($products)) {
                    foreach ($products as $pIdx => $prodData) {
                        if (!is_array($prodData)) continue;

                        $prodName = trim($prodData['name'] ?? ($prodData['product_name'] ?? 'Product'));
                        $primaryCatId = $prodData['primary_category_id'] ?? ($prodData['categories']['primary_category_id'] ?? null);
                        $primarySubcatId = $prodData['primary_subcategory_id'] ?? ($prodData['categories']['primary_subcategory_id'] ?? null);
                        $categoryIds = $prodData['category_ids'] ?? ($prodData['categories']['category_ids'] ?? []);
                        $subcategoryIds = $prodData['subcategory_ids'] ?? ($prodData['categories']['subcategory_ids'] ?? []);

                        $hasPlans = !empty($prodData['plans']);

                        $product = BusinessOfferProduct::updateOrCreate(
                            [
                                'business_id' => $business->id,
                                'name' => $prodName,
                            ],
                            [
                                'product_id' => $prodData['product_id'] ?? null,
                                'description' => $prodData['description'] ?? null,
                                'primary_category_id' => $primaryCatId,
                                'primary_subcategory_id' => $primarySubcatId,
                                'category_ids' => is_array($categoryIds) ? $categoryIds : [],
                                'subcategory_ids' => is_array($subcategoryIds) ? $subcategoryIds : [],
                                'has_plans' => $hasPlans,
                                'sort_order' => $pIdx,
                                'status' => $prodData['status'] ?? 'public',
                            ]
                        );
                        $importedProductsCount++;

                        if ($hasPlans) {
                            $product->directPrices()->delete();
                            $product->directFeatures()->delete();

                            foreach ($prodData['plans'] as $plIdx => $plData) {
                                if (!is_array($plData)) continue;

                                $planName = trim($plData['name'] ?? ($plData['plan_name'] ?? ('Plan #' . ($plIdx + 1))));
                                $plan = BusinessOfferPlan::updateOrCreate(
                                    [
                                        'business_product_id' => $product->id,
                                        'name' => $planName,
                                    ],
                                    [
                                        'plan_id' => $plData['plan_id'] ?? null,
                                        'short_description' => $plData['short_description'] ?? ($plData['description'] ?? null),
                                        'names_by_locale' => $plData['names_by_locale'] ?? [],
                                        'is_featured' => !empty($plData['is_featured']),
                                        'sort_order' => $plIdx,
                                        'status' => $plData['status'] ?? 'public',
                                    ]
                                );
                                $importedPlansCount++;

                                $plan->prices()->delete();
                                $prices = $plData['prices'] ?? [];
                                if (is_array($prices)) {
                                    foreach ($prices as $pr) {
                                        $regPrice = (float)($pr['regular_price'] ?? ($pr['amount'] ?? 0));
                                        $renPrice = (isset($pr['renewal_price']) && $pr['renewal_price'] !== '') ? (float)$pr['renewal_price'] : null;
                                        $introPrice = (isset($pr['intro_price']) && $pr['intro_price'] !== '') ? (float)$pr['intro_price'] : ((isset($pr['intro']) && $pr['intro'] !== '') ? (float)$pr['intro'] : null);
                                        $billingPeriod = $pr['billing_period'] ?? ($pr['term'] ?? '1m');
                                        $priceModel = ($regPrice == 0.0 && ($billingPeriod === 'none' || $billingPeriod === 'free')) ? 'free' : ($pr['price_model'] ?? 'fixed');

                                        BusinessOfferPrice::create([
                                            'business_plan_id' => $plan->id,
                                            'country_region' => $pr['country_region'] ?? ($pr['market'] ?? 'all'),
                                            'currency' => strtoupper($pr['currency'] ?? 'USD'),
                                            'billing_period' => $billingPeriod,
                                            'commitment_period' => $pr['commitment_period'] ?? null,
                                            'unit' => $pr['unit'] ?? null,
                                            'regular_price' => $regPrice,
                                            'renewal_price' => $renPrice,
                                            'intro_price' => $introPrice,
                                            'intro_duration' => $pr['intro_duration'] ?? ($pr['duration'] ?? null),
                                            'price_model' => $priceModel,
                                            'is_active' => true,
                                        ]);
                                    }
                                }

                                $plan->planFeatures()->delete();
                                $features = $plData['features'] ?? ($plData['feature_ids'] ?? []);
                                $featuredIds = $plData['featured_feature_ids'] ?? ($plData['featured_highlights'] ?? []);
                                if (is_array($features)) {
                                    foreach ($features as $fIdx => $fId) {
                                        $featureId = is_numeric($fId) ? (int)$fId : Feature::whereHas('translations', fn($t) => $t->where('name', $fId))->value('id');
                                        if ($featureId) {
                                            BusinessOfferPlanFeature::create([
                                                'business_plan_id' => $plan->id,
                                                'feature_id' => $featureId,
                                                'is_featured_highlight' => in_array($fId, $featuredIds) || in_array($featureId, $featuredIds),
                                                'sort_order' => $fIdx,
                                            ]);
                                        }
                                    }
                                }

                                $plan->specifications()->delete();
                                $specs = $plData['specifications'] ?? ($plData['specs'] ?? []);
                                if (is_array($specs)) {
                                    foreach ($specs as $sp) {
                                        if (!empty($sp['code'])) {
                                            BusinessOfferSpecification::create([
                                                'business_plan_id' => $plan->id,
                                                'code' => $sp['code'],
                                                'value' => (string)($sp['value'] ?? ''),
                                                'unit' => $sp['unit'] ?? null,
                                            ]);
                                        }
                                    }
                                }
                            }
                        } else {
                            $product->directPrices()->delete();
                            $prices = $prodData['prices'] ?? [];
                            if (is_array($prices)) {
                                foreach ($prices as $pr) {
                                    $regPrice = (float)($pr['regular_price'] ?? ($pr['amount'] ?? 0));
                                    $priceModel = ($regPrice == 0.0) ? 'free' : ($pr['price_model'] ?? 'fixed');

                                    BusinessOfferPrice::create([
                                        'business_product_id' => $product->id,
                                        'country_region' => $pr['country_region'] ?? ($pr['market'] ?? 'all'),
                                        'currency' => strtoupper($pr['currency'] ?? 'USD'),
                                        'billing_period' => 'once',
                                        'unit' => $pr['unit'] ?? null,
                                        'regular_price' => $regPrice,
                                        'renewal_price' => null,
                                        'price_model' => $priceModel,
                                        'is_active' => true,
                                    ]);
                                }
                            }

                            $product->directFeatures()->delete();
                            $features = $prodData['features'] ?? ($prodData['feature_ids'] ?? []);
                            $featuredIds = $prodData['featured_feature_ids'] ?? ($prodData['featured_highlights'] ?? []);
                            if (is_array($features)) {
                                foreach ($features as $fIdx => $fId) {
                                    $featureId = is_numeric($fId) ? (int)$fId : Feature::whereHas('translations', fn($t) => $t->where('name', $fId))->value('id');
                                    if ($featureId) {
                                        BusinessOfferPlanFeature::create([
                                            'business_product_id' => $product->id,
                                            'feature_id' => $featureId,
                                            'is_featured_highlight' => in_array($fId, $featuredIds) || in_array($featureId, $featuredIds),
                                            'sort_order' => $fIdx,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                $faqs = $bData['pricing_faqs'] ?? ($bData['faqs'] ?? []);
                if (!empty($faqs) && is_array($faqs)) {
                    $hasPlans = $business->hasOfferPlans();
                    $faqCategoryName = $hasPlans ? 'Plans & Pricing' : 'Pricing';
                    $faqCat = BusinessFaqCategory::firstOrCreate(
                        [
                            'business_id' => $business->id,
                            'name' => $faqCategoryName,
                        ],
                        [
                            'position' => 2,
                            'status' => 1,
                        ]
                    );

                    $langId = getCurrentLanguageID() ?: 1;

                    foreach ($faqs as $faqItem) {
                        if (!is_array($faqItem)) continue;
                        $qText = trim($faqItem['question'] ?? ($faqItem['q'] ?? ''));
                        $aText = trim($faqItem['answer'] ?? ($faqItem['a'] ?? ''));
                        if ($qText === '' || $aText === '') continue;

                        $existingFaq = BusinessFaq::where('business_id', $business->id)
                            ->where('business_faq_category_id', $faqCat->id)
                            ->whereHas('translations', function($t) use ($qText) {
                                $t->where('question', $qText);
                            })->first();

                        if ($existingFaq) {
                            $trans = BusinessFaqTranslation::where('business_faq_id', $existingFaq->id)
                                ->where('lang_id', $langId)
                                ->first();
                            if ($trans) {
                                $trans->answer = $aText;
                                $trans->save();
                            } else {
                                BusinessFaqTranslation::create([
                                    'business_faq_id' => $existingFaq->id,
                                    'lang_id' => $langId,
                                    'question' => $qText,
                                    'answer' => $aText,
                                ]);
                            }
                        } else {
                            $faq = BusinessFaq::create([
                                'business_id' => $business->id,
                                'business_faq_category_id' => $faqCat->id,
                                'position' => BusinessFaq::where('business_faq_category_id', $faqCat->id)->count() + 1,
                                'status' => 1,
                            ]);

                            BusinessFaqTranslation::create([
                                'business_faq_id' => $faq->id,
                                'lang_id' => $langId,
                                'question' => $qText,
                                'answer' => $aText,
                            ]);
                        }
                        $importedFaqsCount++;
                    }
                }

                $business->pricing_updated_at = now();
                $business->save();
                $importedCount++;
            }

            DB::commit();

            if ($importedCount === 0) {
                $msg = 'No businesses could be imported.';
                if (!empty($skippedBusinesses)) {
                    $msg .= ' The following business identifiers were not found in the directory: ' . implode(', ', $skippedBusinesses);
                }
                return back()->with('error', $msg);
            }

            $successMsg = "Successfully imported products and prices for {$importedCount} " . Str::plural('business', $importedCount) . " ({$importedProductsCount} " . Str::plural('product', $importedProductsCount) . ", {$importedPlansCount} " . Str::plural('plan', $importedPlansCount) . ($importedFaqsCount > 0 ? ", {$importedFaqsCount} FAQs" : "") . ").";
            if (!empty($skippedBusinesses)) {
                $successMsg .= " Skipped businesses not found: " . implode(', ', $skippedBusinesses);
            }

            return back()->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function addPriceFaq(Request $request, $businessId)
    {
        $business = Business::findOrFail($businessId);

        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
        ]);

        $hasPlans = $business->hasOfferPlans();
        $categoryName = $hasPlans ? 'Plans & Pricing' : 'Pricing';

        $category = BusinessFaqCategory::firstOrCreate(
            [
                'business_id' => $business->id,
                'name' => $categoryName,
            ],
            [
                'position' => 2,
                'status' => 1,
            ]
        );

        BusinessFaqCategory::where('business_id', $business->id)
            ->where('id', '!=', $category->id)
            ->where('position', '>=', 2)
            ->increment('position');

        $category->position = 2;
        $category->save();

        $faq = BusinessFaq::create([
            'business_id' => $business->id,
            'business_faq_category_id' => $category->id,
            'position' => BusinessFaq::where('business_faq_category_id', $category->id)->count() + 1,
            'status' => 1,
        ]);

        BusinessFaqTranslation::create([
            'business_faq_id' => $faq->id,
            'lang_id' => getCurrentLanguageID(),
            'question' => $request->input('question'),
            'answer' => $request->input('answer'),
        ]);

        $business->pricing_updated_at = now();
        $business->save();

        return response()->json(['success' => true, 'message' => 'FAQ added to ' . $categoryName . ' successfully.']);
    }
}
