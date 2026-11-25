<?php

namespace App\Http\Controllers;

use App\Services\AffiliateTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostbackController extends Controller
{
    /**
     * Handle incoming server-to-server (S2S) affiliate conversion postbacks.
     */
    public function handle(Request $request)
    {
        $request->validate([
            'sid' => 'required|string|max:191',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $clickId = trim($request->input('sid'));
        $amount = floatval($request->input('amount', 0));

        if ($clickId && AffiliateTrackingService::recordConversion($clickId, $amount)) {
            Log::info("Affiliate conversion tracked: ClickID {$clickId}, Amount: {$amount}, IP: " . $request->ip());
            return response()->json(['status' => 'ok']);
        }

        Log::warning("Affiliate conversion postback failed or clickId invalid: ClickID {$clickId}, IP: " . $request->ip());
        return response()->json(['status' => 'error', 'message' => 'Invalid or duplicate click ID'], 400);
    }
}
