@extends('admin_layout.master')
@section('content')
<style>
    span#select2-countrySelect-container {
        padding-right: 3rem;
    }
    .select2-container--default .select2-selection--single {
        border-color: #dbdfea;
        background-color: #fff;
        border-radius: 4px;
        height: calc(2.125rem + 2px);
    }
</style>

<div class="nk-block nk-block-lg site-content">
    <div class="nk-block-head">
        <div class="nk-block-between align-items-center">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Manage Text Content</h4>
            </div>
            {{-- Country/Region Selector --}}
            <div class="nk-block-head-content">
                <div class="form-group mb-0">
                    <div class="form-control-wrap">
                        <select class="form-select js-select2" data-placeholder="Select Country/Region" id="countrySelect" onchange="filterContentByCountry(this.value)">
                            @if(isset($languages))
                                @foreach ($languages as $language)
                                    <option value="{{ $language->lang_code }}" {{ (isset($langCode) && $langCode == $language->lang_code) || (isset($langId) && $langId == $language->id) ? 'selected' : '' }}>
                                        {{ $language->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-bordered">
        <div class="card-inner">
            <div class="row g-4 gy-5">
                {{-- 1. URL Slugs / Page URL Translations Section --}}
                <div class="col-12 mb-3">
                    <form action="{{ route('admin.text-content.update') }}" class="form-validate" method="POST">
                        @csrf
                        <input type="hidden" name="lang_id" value="{{ $langId }}">

                        <div class="card border w-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Page URL Slugs</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="faq_slug">FAQ URL Slug (e.g. faqs, preguntas-frecuentes)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="faq_slug" name="slugs[faq_slug]"
                                                    value="{{ old('slugs.faq_slug', $currentLanguage->faq_slug ?? 'faqs') }}" placeholder="faqs" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="alternatives_slug">Alternatives URL Slug (e.g. alternatives, alternativas)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="alternatives_slug" name="slugs[alternatives_slug]"
                                                    value="{{ old('slugs.alternatives_slug', $currentLanguage->alternatives_slug ?? 'alternatives') }}" placeholder="alternatives" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="reviews_slug">Reviews URL Slug (e.g. reviews, resenas, all-review)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="reviews_slug" name="slugs[reviews_slug]"
                                                    value="{{ old('slugs.reviews_slug', $currentLanguage->reviews_slug ?? 'reviews') }}" placeholder="reviews" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="comparisons_slug">Comparisons URL Slug (e.g. comparisons, comparativas)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="comparisons_slug" name="slugs[comparisons_slug]"
                                                    value="{{ old('slugs.comparisons_slug', $currentLanguage->comparisons_slug ?? 'comparisons') }}" placeholder="comparisons" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary btn-localio">
                                        Update Page URL Slugs
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- 2. Other Text Content Sections --}}
                @foreach ($sections as $sectionKey => $section)
                    <div class="col-12 mb-3">
                        <form action="{{ route('admin.text-content.update') }}" class="form-validate" method="POST">
                            @csrf
                            <input type="hidden" name="lang_id" value="{{ $langId }}">

                            <div class="card border w-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">{{ $section['title'] }}</h6>
                                </div>
                                <div class="card-body">
                                    @if ($sectionKey === 'rating_labels')
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 20%;">Rating Range</th>
                                                        <th style="width: 25%;">Min Threshold (0.0 - 5.0)</th>
                                                        <th style="width: 55%;">Word Label for Current Language ({{ $currentLanguage->name ?? 'English' }})</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $tiers = [
                                                            ['range' => '4.5 – 5.0', 'label_key' => 'rating_label_excellent', 'thresh_key' => 'rating_threshold_excellent', 'default_word' => 'Excellent', 'default_t' => '4.5'],
                                                            ['range' => '4.0 – 4.4', 'label_key' => 'rating_label_great', 'thresh_key' => 'rating_threshold_great', 'default_word' => 'Great', 'default_t' => '4.0'],
                                                            ['range' => '3.0 – 3.9', 'label_key' => 'rating_label_good', 'thresh_key' => 'rating_threshold_good', 'default_word' => 'Good', 'default_t' => '3.0'],
                                                            ['range' => '2.0 – 2.9', 'label_key' => 'rating_label_satisfactory', 'thresh_key' => 'rating_threshold_satisfactory', 'default_word' => 'Satisfactory', 'default_t' => '2.0'],
                                                            ['range' => '1.0 – 1.9', 'label_key' => 'rating_label_poor', 'thresh_key' => 'rating_threshold_poor', 'default_word' => 'Poor', 'default_t' => '1.0'],
                                                        ];
                                                    @endphp
                                                    @foreach ($tiers as $tier)
                                                        @php
                                                            $lblObj = $keys[$tier['label_key']] ?? null;
                                                            $lblVal = '';
                                                            if ($lblObj) {
                                                                if ($langId == 1) {
                                                                    $lblVal = $lblObj->default_value ?? $tier['default_word'];
                                                                } else {
                                                                    $trans = $lblObj->translations->first();
                                                                    $lblVal = $trans ? $trans->value : ($lblObj->default_value ?? $tier['default_word']);
                                                                }
                                                            } else {
                                                                $lblVal = $tier['default_word'];
                                                            }

                                                            $thrObj = $keys[$tier['thresh_key']] ?? null;
                                                            $thrVal = $thrObj->default_value ?? $tier['default_t'];
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-primary fs-13px">{{ $tier['range'] }}</span>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.1" min="0" max="5" class="form-control"
                                                                    name="texts[{{ $tier['thresh_key'] }}]" value="{{ $thrVal }}" placeholder="{{ $tier['default_t'] }}" />
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control fw-bold"
                                                                    name="texts[{{ $tier['label_key'] }}]" value="{{ $lblVal }}" placeholder="{{ $tier['default_word'] }}" />
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                    <div class="row g-3">
                                        @foreach ($section['keys'] as $key)
                                            @php
                                                $keyObj = $keys[$key] ?? null;
                                                $value = '';
                                                if ($keyObj) {
                                                    if ($langId == 1) {
                                                        $value = $keyObj->default_value ?? '';
                                                    } else {
                                                        $trans = $keyObj->translations->first();
                                                        $value = $trans ? $trans->value : ($keyObj->default_value ?? '');
                                                    }
                                                }
                                            @endphp
                                            @php
                                                $keyLabels = [
                                                    'profile_and_reviews' => 'Profile & Reviews Button (formerly "View details")',
                                                    'visit_website' => 'Visit Website Button',
                                                    'category_badge_label' => 'Top Rated Badge (formerly "Top choice")',
                                                    'recommend_this' => 'Recommendation Text (e.g. "of users recommend")',
                                                    'product_badge_label' => 'Product Feature Badge (e.g. "Key Features")',
                                                    'compare_search_placeholder' => 'Compare Tool Search Placeholder (e.g. "Search by name...")',
                                                    'already_to_localio' => 'Already Have an Account Text (e.g. "Already have an Account?")',
                                                    'reviews_word' => 'Word for "reviews" (e.g. "reviews")',
                                                    'review_breakdown_heading' => 'Review Breakdown Card Heading',
                                                    'recommended_by_users' => 'Recommended By Users Label',
                                                    'pros_label' => 'Pros Label',
                                                    'cons_label' => 'Cons Label',
                                                    'submit_your_review_heading' => 'Submit Your Review Heading',
                                                    'write_a_review_placeholder' => 'Write a Review Placeholder',
                                                    'submit_review_button' => 'Submit Review Button Text',
                                                    'share_experience_subheading' => 'Share Experience Subheading',
                                                    'not_rated_yet' => 'Not Rated Yet Badge Text',
                                                    'filter_by_rating_label' => 'Filter by Rating Dropdown Label',
                                                    'sort_label' => 'Sort Label Prefix',
                                                    'sort_most_recent' => 'Sort Option: Most Recent',
                                                    'sort_highest_rated' => 'Sort Option: Highest Rated',
                                                    'sort_lowest_rated' => 'Sort Option: Lowest Rated',
                                                    'claim_now_button' => 'Claim Now Button Text',
                                                    'highlighted_reviews_heading' => 'Highlighted Reviews Section Heading',
                                                    'recent_discussions_heading' => 'Recent Discussions Section Heading',
                                                    'no_comparisons_available' => 'No Comparisons Available Notice',
                                                    'no_reviews_found' => 'No Reviews Found Notice',
                                                    'view_original_translation' => 'View Original Translation Toggle Link',
                                                    'tab_description' => 'Navigation Tab: Description',
                                                    'tab_alternatives' => 'Navigation Tab: Alternatives',
                                                    'tab_faqs' => 'Navigation Tab: FAQs',
                                                    'tab_compare' => 'Navigation Tab: Compare',
                                                    'tab_reviews' => 'Navigation Tab: Reviews',
                                                    'tab_discussions' => 'Navigation Tab: Discussions',
                                                    'more_about_business_heading' => 'More About Business Sidebar Heading',
                                                    'your_comparison_heading' => 'Your Comparison Matrix Heading',
                                                    'search_product_to_compare_placeholder' => 'Search Product to Compare Placeholder',
                                                    'all_products_already_added' => 'All Products Already Added Notice',
                                                    'no_other_products_in_category' => 'No Other Products in Category Notice',
                                                    'detailed_user_ratings_heading' => 'Detailed User Ratings Section Heading',
                                                    'no_criteria_available' => 'No Criteria Available Notice',
                                                    'add_to_compare_button' => 'Add to Compare Button Text',
                                                    'no_description_available' => 'No Description Available Notice',
                                                    'no_pros_added_yet' => 'No Pros Added Yet Notice',
                                                    'no_cons_added_yet' => 'No Cons Added Yet Notice',
                                                    'starting_price_label' => 'Starting Price Label',
                                                    'contact_pricing_label' => 'Contact Pricing Label',
                                                    'custom_subscription_label' => 'Custom Subscription Badge Text',
                                                    'view_pricing_link' => 'View Pricing Link Text',
                                                    'free_trial_label' => 'Free Trial Status Label',
                                                    'available_label' => 'Available Status Badge Text',
                                                    'popular_comparisons_heading' => 'Popular Comparisons Section Heading',
                                                    'all_filter_option' => 'All Filter Option',
                                                    'real_ratings_badge' => 'Real Ratings Trust Badge Text',
                                                    'selected_features_label' => 'Selected Features Label',
                                                    'clear_features_button' => 'Clear Features Button Text',
                                                    'no_matches_found' => 'No Matches Found Notice',
                                                    'no_pros_available' => 'No Pros Available Notice',
                                                    'no_cons_available' => 'No Cons Available Notice',
                                                    'overall_rating_label' => 'Overall Rating Score Title',
                                                    'recommend_label' => 'Recommend Column/Table Header',
                                                    'most_mentioned_pros_cons_subtext' => 'Most Mentioned Pros & Cons Subtext',
                                                    'most_mentioned_pro_label' => 'Most Mentioned Pro Tag Label',
                                                    'most_mentioned_con_label' => 'Most Mentioned Con Tag Label',
                                                    'current_listing_badge' => 'Current Listing Badge Text',
                                                    'no_businesses_found_search' => 'No Businesses Found Search Notice',
                                                    'active_today_label' => 'Active Today Counter Label',
                                                    'sort_top_popular' => 'Sort Option: Top / Popular',
                                                    'sort_most_recent_disc' => 'Sort Option: Most Recent (Discussions)',
                                                    'sort_most_active_disc' => 'Sort Option: Most Active (Discussions)',
                                                    'pinned_discussion_badge' => 'Pinned Discussion Badge Text',
                                                    'what_are_your_thoughts_placeholder' => 'What Are Your Thoughts Placeholder',
                                                    'want_to_join_discussion_prompt' => 'Want to Join Discussion Prompt',
                                                    'search_discussions_placeholder' => 'Search Discussions Input Placeholder',
                                                    'discussion_title_field_label' => 'Discussion Title Field Label',
                                                    'discussion_title_placeholder' => 'Discussion Title Input Placeholder',
                                                    'discussion_details_field_label' => 'Discussion Details Field Label',
                                                    'discussion_details_placeholder' => 'Discussion Details Input Placeholder',
                                                    'no_discussions_match' => 'No Discussions Match Notice',
                                                    'recommend_yes_option' => 'Recommend Option: Yes',
                                                    'recommend_no_option' => 'Recommend Option: No',
                                                    'rating_required_error' => 'Rating Required Validation Error Text',
                                                    'review_title_placeholder' => 'Review Title Placeholder',
                                                    'review_text_placeholder' => 'Review Textarea Placeholder',
                                                    'review_pros_subtext' => 'Review Pros Subtitle (What you liked)',
                                                    'review_cons_subtext' => 'Review Cons Subtitle (What you disliked)',
                                                    'search_pros_placeholder' => 'Search or Filter Pros Placeholder',
                                                    'search_cons_placeholder' => 'Search or Filter Cons Placeholder',
                                                    'do_you_recommend_question' => 'Do You Recommend Question Prefix',
                                                    'rating_guideline_1' => 'Review Modal Guideline 1',
                                                    'rating_guideline_2' => 'Review Modal Guideline 2',
                                                    'rating_guideline_3' => 'Review Modal Guideline 3',
                                                    'rating_guideline_4' => 'Review Modal Guideline 4',
                                                    'report_a_review_heading' => 'Report a Review Modal Heading',
                                                    'select_issue_reason_subtext' => 'Select Reason Subtext',
                                                    'additional_details_optional' => 'Additional Details Optional Label',
                                                    'additional_details_placeholder' => 'Additional Details Input Placeholder',
                                                    'cancel_button' => 'Cancel Button Text',
                                                    'submit_report_button' => 'Submit Report Button Text',
                                                    'was_this_helpful_question' => 'Was This Helpful Question',
                                                    'helpful_yes' => 'Helpful Button: Yes',
                                                    'helpful_no' => 'Helpful Button: No',
                                                    'write_review_hero_title' => 'Write Review Hero Title',
                                                    'write_review_hero_subtitle' => 'Write Review Hero Subtitle',
                                                    'write_review_search_placeholder' => 'Write Review Search Placeholder',
                                                    'popular_with_community_title' => 'Popular With Community Title',
                                                    'popular_with_community_desc' => 'Popular With Community Description',
                                                    'be_first_to_review_title' => 'Be the First to Review Title',
                                                    'be_first_to_review_desc' => 'Be the First to Review Description',
                                                    'recently_reviewed_title' => 'Recently Reviewed Title',
                                                    'recently_reviewed_desc' => 'Recently Reviewed Description',
                                                    'review_card_button' => 'Review Card Action Button Text',
                                                    'choose_country_region_heading' => 'Choose Country/Region Modal Heading',
                                                    'disclosure_label' => 'Commission Disclosure Prefix Label',
                                                    'community_guidelines_link_text' => 'Community Guidelines Link Text',
                                                    'sign_in_or_create_account_title' => 'Sign In or Create Account Modal Title',
                                                    'continue_with_google_button' => 'Continue with Google Button Text',
                                                    'continue_button' => 'Continue Form Button Text',
                                                    'create_your_profile_title' => 'Create Your Profile Modal Title',
                                                    'first_name_placeholder' => 'First Name Placeholder',
                                                    'last_name_placeholder' => 'Last Name Placeholder',
                                                    'job_title_placeholder' => 'Job Title Placeholder',
                                                    'company_size_dropdown_label' => 'Company Size Dropdown Label',
                                                    'categories_nav_label' => 'Categories Navigation Link Label',
                                                    'share_this_heading' => 'Share This Modal Heading',
                                                    'copy_link_button' => 'Copy Link Button Text',
                                                    'copied_toast' => 'Copied! Toast Feedback Text',
                                                    'all_rights_reserved_text' => 'All Rights Reserved Footer Text',
                                                    'go_to_reviews_link' => 'Go to Reviews Link Text',
                                                    'go_to_discussions_link' => 'Go to Discussions Link Text',
                                                    'free_trial_available_heading' => 'Free Trial Available Badge Heading',
                                                    'pricing_per_prefix' => 'Pricing Period Prefix (e.g. per)',
                                                    'community_sentiment_heading' => 'Community Sentiment Section Heading',
                                                    'last_updated_prefix' => 'Last Updated Prefix Text',
                                                    'was_this_answer_helpful' => 'Was This Answer Helpful Question',
                                                    'still_have_questions_prompt' => 'Still Have Questions Prompt Heading',
                                                    'ask_community_button' => 'Ask in the Community Button Text',
                                                    'join_community_heading' => 'Join Localio Community Section Title',
                                                    'join_community_subheading' => 'Join Localio Community Description',
                                                    'join_benefit_share_exp' => 'Join Benefit: Share Your Experience',
                                                    'join_benefit_discussions' => 'Join Benefit: Join Discussions',
                                                    'join_benefit_reputation' => 'Join Benefit: Build Your Reputation',
                                                    'get_started_button' => 'Get Started Action Button Text',

                                                    // Error & Notification Messages
                                                    'error_auth_invalid_credentials' => 'Auth Error: Invalid Email or Password',
                                                    'error_auth_account_deactivated' => 'Auth Error: Account Deactivated',
                                                    'error_auth_session_expired' => 'Auth Error: Session Expired',
                                                    'error_auth_user_not_found' => 'Auth Error: User Not Found',
                                                    'error_auth_otp_invalid' => 'Auth Error: OTP Incorrect or Expired',
                                                    'error_auth_failed' => 'Auth Error: Authentication Failed',
                                                    'error_auth_social_failed' => 'Auth Error: Social Sign-in Failed',
                                                    'error_auth_registration_failed' => 'Auth Error: Registration Failed',
                                                    'error_login_required' => 'Account Error: Login Required',
                                                    'error_password_current_incorrect' => 'Password Error: Current Password Incorrect',
                                                    'error_password_same_as_current' => 'Password Error: Cannot Be Same as Current',
                                                    'error_password_min_length' => 'Password Error: Minimum 8 Characters',
                                                    'error_review_login_required' => 'Review Error: Must Be Logged In',
                                                    'error_review_recommend_required' => 'Review Error: Recommendation Required',
                                                    'error_review_submit_failed' => 'Review Error: Submit Failed',
                                                    'error_review_not_found' => 'Review Error: Review Not Found',
                                                    'error_discussion_login_post' => 'Discussion Error: Sign In to Post',
                                                    'error_discussion_login_comment' => 'Discussion Error: Sign In to Comment',
                                                    'error_discussion_login_vote' => 'Discussion Error: Sign In to Vote',
                                                    'error_discussion_login_pin' => 'Discussion Error: Login to Pin Items',
                                                    'error_permission_denied' => 'Permission Error: Permission Denied',
                                                    'msg_discussion_posted_success' => 'Discussion Success: Posted Successfully',
                                                    'msg_comment_posted_success' => 'Discussion Success: Comment Posted',
                                                    'msg_discussion_deleted_success' => 'Discussion Success: Discussion Deleted',
                                                    'msg_comment_deleted_success' => 'Discussion Success: Comment Deleted',
                                                    'error_compare_max_limit' => 'Comparison Error: Max 4 Products Limit',
                                                    'error_product_not_found' => 'Comparison Error: Product Not Found',
                                                    'error_report_login_issue' => 'Report Error: Sign In to Report Issue',
                                                    'error_report_login_review' => 'Report Error: Sign In to Report Review',
                                                    'msg_report_submitted_success' => 'Report Success: Report Submitted',
                                                    'msg_review_report_success' => 'Report Success: Review Report Submitted',
                                                    'error_country_not_found' => 'General Error: Country/Region Not Found',
                                                    'error_general_something_went_wrong' => 'General Error: Something Went Wrong',
                                                    'error_translation_unavailable' => 'Translation Error: Unavailable Notice',
                                                    'error_connection_issue' => 'Network Error: Connection Issue Notice',
                                                ];
                                                $displayLabel = $keyLabels[$key] ?? ucwords(str_replace('_', ' ', $key));

                                                $keyHints = [
                                                    'category_badge_label' => 'Displays in the star pill badge on top recommended listings (e.g., "Top rated").',
                                                    'recommend_this' => 'Displays after the percentage on category/top-rated listing cards (e.g., "100% of users recommend").',
                                                    'product_badge_label' => 'Displays in the USP/features badge on the product detail page (e.g., "Key Features").',
                                                    'compare_search_placeholder' => 'Placeholder text for the category page comparison tool search inputs (e.g., "Search by name...")',
                                                    'already_to_localio' => 'Displays on the registration pages next to the login link.',
                                                    'reviews_word' => 'Used in review count labels and comparison headers.',
                                                ];
                                                $displayHint = $keyHints[$key] ?? null;
                                            @endphp
                                            <div class="form-group col-lg-12">
                                                <label class="form-label" for="{{ $key }}">
                                                    {{ $displayLabel }}
                                                </label>
                                                <div class="form-control-wrap">
                                                    @if (str_contains($key, '_des') || str_contains($key, 'mail_below_text') || str_contains($key, '_desc') || str_contains($key, '_message'))
                                                        <textarea class="form-control" id="{{ $key }}"
                                                            name="texts[{{ $key }}]" rows="3">{{ $value }}</textarea>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            id="{{ $key }}" name="texts[{{ $key }}]" value="{{ $value }}" />
                                                    @endif
                                                </div>
                                                @if ($displayHint)
                                                    <div class="form-note text-muted mt-1">{{ $displayHint }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-primary btn-localio">
                                            Update {{ $section['title'] }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function filterContentByCountry(langCode) {
        if (!langCode) return;
        let url = new URL(window.location.href);
        url.searchParams.set('lang', langCode);
        window.location.href = url.toString();
    }

    $(document).ready(function () {
        $('#countrySelect').on('change select2:select', function () {
            let langCode = $(this).val();
            filterContentByCountry(langCode);
        });
    });
</script>
@endsection
