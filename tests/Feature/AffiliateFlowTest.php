<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;



use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

use App\Models\Business;
use App\Models\AffiliateClick;



class AffiliateFlowTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    // use DatabaseTransactions;

    public function test_affiliate_click_and_postback_conversion()
    {
        // Use an existing business (replace 1 with any valid business ID in your DB)
        $business = Business::find(19);

        $this->assertNotNull($business, 'Business with ID 1 must exist for this test.');

        // Simulate a session with gclid
        Session::start();
        session(['gclid' => 'GCLID_123']);

        // Trigger the click tracking
        $clickUrl = \App\Services\AffiliateTrackingService::trackClick($business);
        preg_match('/sid=([a-zA-Z0-9_]+)/', $clickUrl, $matches);
        $clickId = $matches[1];

        // Confirm click saved in DB
        $this->assertDatabaseHas('affiliate_clicks', [
            'click_id' => $clickId,
            'business_id' => $business->id,
            'converted' => false,
        ]);

        // Simulate postback
        $response = $this->postJson('/postback', [
            'sid' => $clickId,
            'amount' => 99.99,
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'ok']);

        // Confirm conversion update
        $this->assertDatabaseHas('affiliate_clicks', [
            'click_id' => $clickId,
            'converted' => true,
            'revenue' => 99.99,
        ]);
    }


}
