<?php
namespace App\Services;

use App\Models\AffiliateClick;
use Illuminate\Support\Str;

class AffiliateTrackingService
{

    public static function trackClick($business)
    {
        if (!$business->affiliate_link) return $business->affiliate_link;
        
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
        $separator = strpos($business->affiliate_link, '?') ? '&' : '?';
        // dd($business->affiliate_link . $separator . $business->subid_param . '=' . $clickId);
        return $business->affiliate_link . $separator . $business->subid_param . '=' . $clickId;
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

?>