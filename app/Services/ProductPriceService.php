<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Country;
use App\Models\ExclusiveDeal;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ProductPriceService
{
    public static function getEffectivePrice(Product $product, ?string $country = null, ?string $priceType = null): float
    {
        // 1. Get country code
        if (!$country) {
            if (Request::has('country_code')) {
                $country = Request::get('country_code');
            } elseif (Auth::check() && Auth::user()->country_id) {
                $countryModel = Country::find(Auth::user()->country_id);
                $country = $countryModel?->country_code ?? 'US';
            } else {
                $country = 'US';
            }
        }

        // 2. Get price type
        $priceType = $priceType
            ?? Auth::user()?->price_type
            ?? Session::get('price_type')
            ?? config('pricing.default_type')
            ?? 'base_price';

        // 3. Get original price
        $price = $product->prices()
            ->where('country_code', $country)
            ->where('price_type', $priceType)
            ->where('status', 'active')
            ->orderByDesc('updated_at')
            ->first();

        if (!$price) {
            return 0.00;
        }

        $basePrice = $price->price;

        // 4. Check for exclusive deal
        $now = Carbon::now();

        $deal = ExclusiveDeal::where('product_id', $product->id)
            ->where('country_code', $country)
            ->where('price_type', $priceType)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->orderByDesc('discount_percent')
            ->first();

        if ($deal) {
            $discount = $deal->discount_percent;
            $finalPrice = round($basePrice * (1 - $discount / 100), 2);
            return $finalPrice;
        }

        return $basePrice;
    }
}
