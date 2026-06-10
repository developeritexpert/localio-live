<?php

namespace App\Http\Controllers;
use App\Services\AffiliateTrackingService;

use Illuminate\Http\Request;

class PostbackController extends Controller
{
    //
    public function handle(Request $request)
    {
        $clickId = 
                   $request->input('sid') ;
               
       

        $amount = floatval($request->input('amount', 0));

        if ($clickId && AffiliateTrackingService::recordConversion($clickId, $amount)) {
            \Log::info("Conversion tracked: $clickId - $amount");
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'error'], 400);
    }
}
