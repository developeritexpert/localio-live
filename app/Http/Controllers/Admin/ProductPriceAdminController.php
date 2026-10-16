<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessOfferProduct;
use App\Models\BusinessOfferPlan;
use App\Models\BusinessOfferPrice;
use App\Models\BusinessOfferPlanFeature;
use App\Models\BusinessOfferSpecification;
use App\Models\BusinessFaq;
use App\Models\BusinessFaqCategory;
use App\Models\BusinessFaqTranslation;
use App\Models\Feature;
use App\Models\FeatureTransalte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

        return view('Admin.product_prices.manage', compact('business', 'features', 'stats'));
    }

    public function storeProduct(Request $request, $businessId)
    {
        $business = Business::findOrFail($businessId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price_model' => 'required|in:fixed,free,quote',
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
            
            if (!empty($plansData)) {
                $keepPlanIds = [];
                foreach ($plansData as $idx => $pData) {
                    $planId = $pData['id'] ?? null;
                    $plan = $planId ? BusinessOfferPlan::where('business_product_id', $product->id)->find($planId) : new BusinessOfferPlan();
                    if (!$plan) $plan = new BusinessOfferPlan();

                    $plan->business_product_id = $product->id;
                    $plan->name = $pData['name'] ?? $product->name;
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
                        BusinessOfferPrice::create([
                            'business_plan_id' => $plan->id,
                            'country_region' => $pr['market'] ?? ($pr['country_region'] ?? 'all'),
                            'currency' => $pr['currency'] ?? 'USD',
                            'billing_period' => $pr['term'] ?? ($pr['billing_period'] ?? '1m'),
                            'commitment_period' => $pr['commitment_period'] ?? null,
                            'custom_months' => !empty($pr['customMonths']) ? (int)$pr['customMonths'] : null,
                            'unit' => $pr['unit'] ?? null,
                            'custom_unit' => $pr['customUnit'] ?? null,
                            'regular_price' => (float)($pr['amount'] ?? ($pr['regular_price'] ?? 0)),
                            'renewal_price' => (isset($pr['renewal_price']) && $pr['renewal_price'] !== '') ? (float)$pr['renewal_price'] : null,
                            'intro_price' => (isset($pr['intro']) && $pr['intro'] !== '') ? (float)$pr['intro'] : null,
                            'intro_duration' => $pr['duration'] ?? null,
                            'price_model' => $request->input('price_model', 'fixed'),
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
                    BusinessOfferPrice::create([
                        'business_product_id' => $product->id,
                        'country_region' => $pr['market'] ?? ($pr['country_region'] ?? 'all'),
                        'currency' => $pr['currency'] ?? 'USD',
                        'billing_period' => $pr['term'] ?? ($pr['billing_period'] ?? '1m'),
                        'unit' => $pr['unit'] ?? null,
                        'regular_price' => (float)($pr['amount'] ?? ($pr['regular_price'] ?? 0)),
                        'renewal_price' => (isset($pr['renewal_price']) && $pr['renewal_price'] !== '') ? (float)$pr['renewal_price'] : null,
                        'price_model' => $request->input('price_model', 'fixed'),
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

        $data = json_decode($jsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->with('error', 'Invalid JSON format: ' . json_last_error_msg());
        }

        $businesses = $data['businesses'] ?? [];
        if (empty($businesses) || !is_array($businesses)) {
            return back()->with('error', 'JSON must contain a non-empty businesses array.');
        }

        DB::beginTransaction();
        try {
            $importedCount = 0;
            foreach ($businesses as $bData) {
                $businessId = $bData['business_id'] ?? null;
                if (!$businessId) continue;

                $business = Business::find($businessId);
                if (!$business) continue;

                $products = $bData['products'] ?? [];
                foreach ($products as $pIdx => $prodData) {
                    $product = BusinessOfferProduct::updateOrCreate(
                        [
                            'business_id' => $business->id,
                            'name' => $prodData['name'] ?? 'Product',
                        ],
                        [
                            'product_id' => $prodData['product_id'] ?? null,
                            'description' => $prodData['description'] ?? null,
                            'primary_category_id' => $prodData['categories']['primary_category_id'] ?? null,
                            'primary_subcategory_id' => $prodData['categories']['primary_subcategory_id'] ?? null,
                            'category_ids' => $prodData['categories']['category_ids'] ?? [],
                            'subcategory_ids' => $prodData['categories']['subcategory_ids'] ?? [],
                            'has_plans' => !empty($prodData['plans']),
                            'sort_order' => $pIdx,
                            'status' => 'public',
                        ]
                    );

                    if (!empty($prodData['plans'])) {
                        foreach ($prodData['plans'] as $plIdx => $plData) {
                            $plan = BusinessOfferPlan::updateOrCreate(
                                [
                                    'business_product_id' => $product->id,
                                    'name' => $plData['name'] ?? 'Plan',
                                ],
                                [
                                    'plan_id' => $plData['plan_id'] ?? null,
                                    'short_description' => $plData['short_description'] ?? null,
                                    'names_by_locale' => $plData['names_by_locale'] ?? [],
                                    'sort_order' => $plIdx,
                                    'status' => 'public',
                                ]
                            );

                            $plan->prices()->delete();
                            if (!empty($plData['prices'])) {
                                foreach ($plData['prices'] as $pr) {
                                    BusinessOfferPrice::create([
                                        'business_plan_id' => $plan->id,
                                        'country_region' => $pr['country_region'] ?? 'all',
                                        'currency' => $pr['currency'] ?? 'USD',
                                        'billing_period' => $pr['billing_period'] ?? '1m',
                                        'commitment_period' => $pr['commitment_period'] ?? null,
                                        'unit' => $pr['unit'] ?? null,
                                        'regular_price' => (float)($pr['regular_price'] ?? 0),
                                        'renewal_price' => isset($pr['renewal_price']) ? (float)$pr['renewal_price'] : null,
                                        'price_model' => ((float)($pr['regular_price'] ?? 0) == 0 && ($pr['billing_period'] ?? '') === 'none') ? 'free' : 'fixed',
                                        'is_active' => true,
                                    ]);
                                }
                            }

                            $plan->planFeatures()->delete();
                            $features = $plData['features'] ?? [];
                            $featuredIds = $plData['featured_feature_ids'] ?? [];
                            foreach ($features as $fIdx => $fId) {
                                BusinessOfferPlanFeature::create([
                                    'business_plan_id' => $plan->id,
                                    'feature_id' => (int)$fId,
                                    'is_featured_highlight' => in_array($fId, $featuredIds),
                                    'sort_order' => $fIdx,
                                ]);
                            }

                            $plan->specifications()->delete();
                            if (!empty($plData['specifications'])) {
                                foreach ($plData['specifications'] as $sp) {
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
                        if (!empty($prodData['prices'])) {
                            foreach ($prodData['prices'] as $pr) {
                                BusinessOfferPrice::create([
                                    'business_product_id' => $product->id,
                                    'country_region' => $pr['country_region'] ?? 'all',
                                    'currency' => $pr['currency'] ?? 'USD',
                                    'billing_period' => $pr['billing_period'] ?? 'once',
                                    'unit' => $pr['unit'] ?? null,
                                    'regular_price' => (float)($pr['regular_price'] ?? 0),
                                    'is_active' => true,
                                ]);
                            }
                        }

                        $product->directFeatures()->delete();
                        $features = $prodData['features'] ?? [];
                        $featuredIds = $prodData['featured_feature_ids'] ?? [];
                        foreach ($features as $fIdx => $fId) {
                            BusinessOfferPlanFeature::create([
                                'business_product_id' => $product->id,
                                'feature_id' => (int)$fId,
                                'is_featured_highlight' => in_array($fId, $featuredIds),
                                'sort_order' => $fIdx,
                            ]);
                        }
                    }
                }

                $business->pricing_updated_at = now();
                $business->save();
                $importedCount++;
            }

            DB::commit();
            return back()->with('success', 'Successfully imported products and prices for ' . $importedCount . ' businesses.');
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
