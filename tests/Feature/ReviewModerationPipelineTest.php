<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Language;
use App\Models\Review;
use App\Models\ReviewModerationLog;
use App\Models\ReviewReport;
use App\Models\ReviewTranslation;
use App\Models\User;
use App\Services\ReviewModerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReviewModerationPipelineTest extends TestCase
{
    protected User $user;
    protected Business $business;
    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email'     => 'reviewer_' . uniqid() . '@example.com',
            'status'    => 'active',
            'user_type' => 'user',
        ]);

        $this->business = Business::first() ?? Business::create(['status' => 1]);
        $this->language = Language::first() ?? Language::create(['name' => 'English', 'lang_code' => 'en-us']);
    }

    /** @test */
    public function layer1_rejects_reviews_that_are_too_short()
    {
        $review = Review::create([
            'user_id'          => $this->user->id,
            'business_id'      => $this->business->id,
            'lang_id'          => $this->language->id,
            'rating'           => 5,
            'status'           => Review::STATUS_PROCESSING,
            'submitted_locale' => 'en-US',
        ]);

        $review->translations()->create([
            'business_id' => $this->business->id,
            'language_id' => $this->language->id,
            'title'       => 'Hi',
            'description' => 'bad', // length < 10
        ]);

        $result = ReviewModerationService::runLayer1Checks($review);
        ReviewModerationService::applyLayer1($review, $result);

        $this->assertFalse($result['pass']);
        $this->assertEquals('too_short', $result['reason']);
        $this->assertEquals(Review::STATUS_PENDING_REVIEW, $review->fresh()->status);
        $this->assertEquals(Review::ESCALATION_LARAVEL, $review->fresh()->escalation_source);
        $this->assertEquals(Review::AI_STATUS_SKIPPED, $review->fresh()->localio_ai_status);
    }

    /** @test */
    public function layer1_rejects_reviews_containing_prohibited_links()
    {
        $review = Review::create([
            'user_id'          => $this->user->id,
            'business_id'      => $this->business->id,
            'lang_id'          => $this->language->id,
            'rating'           => 4,
            'status'           => Review::STATUS_PROCESSING,
            'submitted_locale' => 'en-US',
        ]);

        $review->translations()->create([
            'business_id' => $this->business->id,
            'language_id' => $this->language->id,
            'title'       => 'Great site',
            'description' => 'Visit my website at https://spammy-affiliate-link.com for free discounts!',
        ]);

        $result = ReviewModerationService::runLayer1Checks($review);
        ReviewModerationService::applyLayer1($review, $result);

        $this->assertFalse($result['pass']);
        $this->assertEquals('prohibited_links', $result['reason']);
        $this->assertEquals(Review::STATUS_PENDING_REVIEW, $review->fresh()->status);
        $this->assertEquals(Review::ESCALATION_LARAVEL, $review->fresh()->escalation_source);
    }

    /** @test */
    public function layer1_rejects_conflict_of_interest_when_vendor_reviews_own_business()
    {
        $vendorUser = User::factory()->create([
            'business_id' => $this->business->id,
            'user_type'   => 'vendor',
        ]);

        $review = Review::create([
            'user_id'          => $vendorUser->id,
            'business_id'      => $this->business->id,
            'lang_id'          => $this->language->id,
            'rating'           => 5,
            'status'           => Review::STATUS_PROCESSING,
            'submitted_locale' => 'en-US',
        ]);

        $review->translations()->create([
            'business_id' => $this->business->id,
            'language_id' => $this->language->id,
            'title'       => 'We are amazing',
            'description' => 'Best service in the entire software world guaranteed!',
        ]);

        $result = ReviewModerationService::runLayer1Checks($review);
        ReviewModerationService::applyLayer1($review, $result);

        $this->assertFalse($result['pass']);
        $this->assertEquals('conflict_of_interest', $result['reason']);
        $this->assertEquals(Review::STATUS_PENDING_REVIEW, $review->fresh()->status);
        $this->assertEquals(Review::ESCALATION_LARAVEL, $review->fresh()->escalation_source);
    }

    /** @test */
    public function layer2_flags_harmful_content_and_skips_gpt()
    {
        config(['openai.api_key' => 'test-api-key']);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response([
                'results' => [
                    [
                        'flagged'    => true,
                        'categories' => [
                            'harassment' => true,
                            'hate'       => false,
                        ],
                    ]
                ]
            ], 200),
        ]);

        $review = Review::create([
            'user_id'          => $this->user->id,
            'business_id'      => $this->business->id,
            'lang_id'          => $this->language->id,
            'rating'           => 1,
            'status'           => Review::STATUS_PROCESSING,
            'submitted_locale' => 'en-US',
        ]);

        $review->translations()->create([
            'business_id' => $this->business->id,
            'language_id' => $this->language->id,
            'title'       => 'Threatening message',
            'description' => 'I will attack and destroy your store in person next week!',
        ]);

        $result = ReviewModerationService::runLayer2Moderation($review);
        ReviewModerationService::applyLayer2($review, $result);

        $this->assertTrue($result['flagged']);
        $this->assertEquals(Review::STATUS_PENDING_REVIEW, $review->fresh()->status);
        $this->assertEquals(Review::ESCALATION_MODERATION_API, $review->fresh()->escalation_source);
        $this->assertEquals(Review::AI_STATUS_SKIPPED, $review->fresh()->localio_ai_status);
        $this->assertFalse($review->fresh()->moderation_api_passed);
    }

    /** @test */
    public function layer3_publishes_clean_review_with_matching_locale()
    {
        $review = Review::create([
            'user_id'               => $this->user->id,
            'business_id'           => $this->business->id,
            'lang_id'               => $this->language->id,
            'rating'                => 5,
            'status'                => Review::STATUS_PROCESSING,
            'submitted_locale'      => 'en-US',
            'moderation_api_passed' => true,
            'localio_ai_status'     => Review::AI_STATUS_PROCESSING,
        ]);

        $gptResult = [
            'decision'         => 'PASS',
            'risk_level'       => 'LOW',
            'flags'            => [],
            'submitted_locale' => 'en-US',
            'detected_locale'  => 'en-US',
            'locale_status'    => 'MATCH',
            'confidence'       => 98,
            'reason'           => 'Genuine positive review with consistent customer feedback.',
        ];

        ReviewModerationService::applyLayer3($review, $gptResult, 'batch_123', 'gpt-5.6-luna', 'pmpt_v1', '1');

        $fresh = $review->fresh();
        $this->assertEquals(Review::STATUS_PUBLISHED, $fresh->status);
        $this->assertEquals('en-US', $fresh->source_locale);
        $this->assertEquals('MATCH', $fresh->locale_status);
        $this->assertEquals('PASS', $fresh->localio_ai_decision);
        $this->assertEquals(Review::AI_STATUS_DONE, $fresh->localio_ai_status);
    }

    /** @test */
    public function layer3_reassigns_locale_and_publishes_when_valid()
    {
        $review = Review::create([
            'user_id'               => $this->user->id,
            'business_id'           => $this->business->id,
            'lang_id'               => $this->language->id,
            'rating'                => 5,
            'status'                => Review::STATUS_PROCESSING,
            'submitted_locale'      => 'en-US',
            'moderation_api_passed' => true,
            'localio_ai_status'     => Review::AI_STATUS_PROCESSING,
        ]);

        $gptResult = [
            'decision'         => 'PASS',
            'risk_level'       => 'LOW',
            'flags'            => [],
            'submitted_locale' => 'en-US',
            'detected_locale'  => 'de-DE',
            'locale_status'    => 'REASSIGNED',
            'confidence'       => 95,
            'reason'           => 'Review submitted on en-US but written in standard German.',
        ];

        ReviewModerationService::applyLayer3($review, $gptResult, 'batch_456', 'gpt-5.6-luna', 'pmpt_v1', '1');

        $fresh = $review->fresh();
        $this->assertEquals(Review::STATUS_PUBLISHED, $fresh->status);
        $this->assertEquals('de-DE', $fresh->source_locale);
        $this->assertEquals('REASSIGNED', $fresh->locale_status);
    }

    /** @test */
    public function layer3_escalates_to_human_when_locale_is_ambiguous()
    {
        $review = Review::create([
            'user_id'               => $this->user->id,
            'business_id'           => $this->business->id,
            'lang_id'               => $this->language->id,
            'rating'                => 4,
            'status'                => Review::STATUS_PROCESSING,
            'submitted_locale'      => 'en-US',
            'moderation_api_passed' => true,
            'localio_ai_status'     => Review::AI_STATUS_PROCESSING,
        ]);

        $gptResult = [
            'decision'         => 'PASS',
            'risk_level'       => 'LOW',
            'flags'            => [],
            'submitted_locale' => 'en-US',
            'detected_locale'  => 'unknown',
            'locale_status'    => 'HUMAN_REVIEW',
            'confidence'       => 40,
            'reason'           => 'Could not confidently identify supported Localio locale.',
        ];

        ReviewModerationService::applyLayer3($review, $gptResult, 'batch_789', 'gpt-5.6-luna', 'pmpt_v1', '1');

        $fresh = $review->fresh();
        $this->assertEquals(Review::STATUS_PENDING_REVIEW, $fresh->status);
        $this->assertEquals(Review::ESCALATION_LOCALIO_AI, $fresh->escalation_source);
        $this->assertEquals('HUMAN_REVIEW', $fresh->locale_status);
    }

    /** @test */
    public function layer3_escalates_to_human_when_integrity_flags_are_detected()
    {
        $review = Review::create([
            'user_id'               => $this->user->id,
            'business_id'           => $this->business->id,
            'lang_id'               => $this->language->id,
            'rating'                => 1,
            'status'                => Review::STATUS_PROCESSING,
            'submitted_locale'      => 'en-US',
            'moderation_api_passed' => true,
            'localio_ai_status'     => Review::AI_STATUS_PROCESSING,
        ]);

        $gptResult = [
            'decision'         => 'HUMAN_REVIEW',
            'risk_level'       => 'CRITICAL',
            'flags'            => ['LEGAL_ALLEGATION'],
            'submitted_locale' => 'en-US',
            'detected_locale'  => 'en-US',
            'locale_status'    => 'MATCH',
            'confidence'       => 94,
            'reason'           => 'The review alleges deliberate criminal financial fraud.',
        ];

        ReviewModerationService::applyLayer3($review, $gptResult, 'batch_999', 'gpt-5.6-luna', 'pmpt_v1', '1');

        $fresh = $review->fresh();
        $this->assertEquals(Review::STATUS_PENDING_REVIEW, $fresh->status);
        $this->assertEquals(Review::ESCALATION_LOCALIO_AI, $fresh->escalation_source);
        $this->assertEquals('HUMAN_REVIEW', $fresh->localio_ai_decision);
    }
}
