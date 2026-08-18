<?php
namespace App\Services;

use App\Models\AffiliateClick;
use Illuminate\Support\Str;

class AffiliateTrackingService
{

    public static function trackClick($business)
    {
        $countryId = function_exists('getCurrentCountry') ? getCurrentCountry() : null;
        $targetUrl = null;
        $isAffiliate = !empty($business->is_affiliate);

        if ($countryId) {
            $countryWebsite = $business->relationLoaded('websites')
                ? $business->websites->firstWhere('country_id', $countryId)
                : $business->websites()->where('country_id', $countryId)->first();

            if ($countryWebsite && !empty($countryWebsite->website_url)) {
                $targetUrl = $countryWebsite->website_url;
                if (isset($countryWebsite->is_affiliate)) {
                    $isAffiliate = (bool)$countryWebsite->is_affiliate;
                }
            }
        }

        if (!$targetUrl) {
            $targetUrl = $business->affiliate_link ?: $business->permanent_url;
        }

        if (!$targetUrl) return '#';

        // If not an affiliate link, return the direct URL
        if (!$isAffiliate) {
            return $targetUrl;
        }

        // Generate unique click ID
        $clickId = 'c_' . Str::random(12) . '_' . time();
        
        // Save click with ad IDs from session
        AffiliateClick::create([
            'click_id' => $clickId,
            'gclid' => session('gclid'),
            'msclkid' => session('msclkid'),
            'business_id' => $business->id,
            'clicked_at' => now()
        ]);
        
        // Add subid to affiliate URL
        $subidParam = !empty($business->subid_param) ? $business->subid_param : 'sid';
        $separator = strpos($targetUrl, '?') !== false ? '&' : '?';
        return $targetUrl . $separator . $subidParam . '=' . $clickId;
    }

    public static function recordConversion($clickId, $amount = 0)
    {
        $click = AffiliateClick::where('click_id', $clickId)->first();

        if ($click && !$click->converted) {
            $click->update([
                'converted' => true,
                'revenue' => $amount,
                'converted_at' => now(),
            ]);
            return true;
        }

        return false;
    }
    

}
