<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\StaticContentKey;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $keys = [
            // Rating Breakdown Word Labels & Thresholds
            'rating_label_excellent' => 'Excellent',
            'rating_threshold_excellent' => '4.5',
            'rating_label_great' => 'Great',
            'rating_threshold_great' => '4.0',
            'rating_label_good' => 'Good',
            'rating_threshold_good' => '3.0',
            'rating_label_satisfactory' => 'Satisfactory',
            'rating_threshold_satisfactory' => '2.0',
            'rating_label_poor' => 'Poor',
            'rating_threshold_poor' => '1.0',

            // Company Size Options
            'company_size_1' => '1-10 employees',
            'company_size_2' => '11-50 employees',
            'company_size_3' => '51-200 employees',
            'company_size_4' => '201+ employees',

            // Community Insights Box & Join Section
            'community_insights_title' => 'Community Insights',
            'community_insights_desc' => 'Verified user reviews and ranking scores updated daily by community feedback.',
            'how_rankings_work' => 'How rankings work',
            'join_community_heading' => 'Join Localio',
            'join_community_subheading' => 'Write reviews, join discussions, and help others make better buying decisions.',
            'join_benefit_share_exp' => 'Share your experience',
            'join_benefit_discussions' => 'Join discussions',
            'join_benefit_reputation' => 'Build your reputation',

            // Listing Badges & Recommendation
            'category_badge_label' => 'Top rated',
            'recommend_this' => 'of users recommend',
            'product_badge_label' => 'Key Features',

            // Card & Listing Action Buttons
            'profile_and_reviews' => 'Profile & reviews',
            'visit_website' => 'Visit website',
            'get_started_button' => 'Get started',

            // Common Page Text
            'compare_search_placeholder' => 'Search by name...',
            'latest_reviews' => 'Latest Reviews',
            'view_more' => 'View More',
            'reviews_word' => 'reviews',

            // Filters Section Text
            'filters' => 'Filters',
            'user_rating' => 'User Rating',
            'price_range' => 'Price Range',
            'reset_filter' => 'Reset All Filters',
            'no_prod_mach_fil' => 'No products found matching your selected filters.',

            // Authentication Text
            'login' => 'Log In',
            'sign_up' => 'Sign Up',
            'forgot_password' => 'Forgot Password?',
            'already_to_localio' => 'Already have an Account?',
            'register_to_localio' => 'Register to Localio',
            'create_an_account' => 'Create an account',

            // Product Section Text
            'exclusive_deals_title' => 'Exclusive Deals',
            'exclusive_deals_des' => 'Discover limited-time exclusive discounts on top software and services.',
            'product_search_placeholder' => 'Search product name',
            'localio_commissions_message' => 'Some links may earn us a commission.',
            'vs_keyword' => 'vs',
            'business_reviews_subheadline' => 'Real reviews, user discussions & alternatives',
            'business_faqs_subheadline' => 'Frequently asked questions and answers about',
            'business_comparisons_subheadline' => 'See how this provider compares to other category providers.',
            'compare_with_any_provider_desc' => 'Select another provider from the search box below to generate an instant side-by-side comparison.',
            'business_alternatives_subheadline' => 'Find the best alternatives to this provider.',
            'alternatives_page_most_popular_headline' => 'Most Popular Alternatives',
            'alternatives_page_most_popular_desc' => 'Discover the most widely used and top-rated alternatives recommended by our community.',

            // Help & Support Text
            'help_search_bar_text' => 'How we can help you?',
            'no_faq_avaliable' => 'No FAQs Available',
            'knowledge_base' => 'Knowledge Base',
            'knowledge_base_desc' => 'Explore helpful insights and resources to guide you through every step of your journey.',
            'no_policy' => 'No privacy policies available in this language.',
            'no_term' => 'No Terms and Conditions available in this language.',
            'contact_us' => 'Contact Us',
            'send_message' => 'Send Message',

            // Mail Text For Category And Top Rated Page
            'top_rated_mail_section_titile' => 'Get the Best Picks in Your Inbox',
            'top_rated_mail_section_desc' => 'Drop your email to receive trusted software picks, all recommended by actual users.',
            'subscribe' => 'Subscribe',
            'mail_below_text' => 'I agree to receive promotional emails from Localio and accept the Privacy Policy and Terms and Conditions. I can unsubscribe at any time.',

            // Review Success Modal
            'review_success_headline' => 'Review Submitted!',
            'review_success_text' => 'Thank you for contributing to the Localio community. Your review helps thousands of buyers make informed decisions.',
            'review_success_close_btn' => 'Done',

            // Product Profile & Listing Cards Text
            'main_offering' => 'Main offering',
            'suitable_for' => 'Suitable for',
            'review_breakdown_heading' => 'Review Breakdown',
            'recommended_by_users' => 'Recommended by users',
            'pros_label' => 'Pros',
            'cons_label' => 'Cons',
            'submit_your_review_heading' => 'Submit your review',
            'write_a_review_placeholder' => 'Write your honest review here...',
            'submit_review_button' => 'Submit Review',
            'share_experience_subheading' => 'Help others by sharing your honest experience with this service.',
            'not_rated_yet' => 'Not rated yet',
            'filter_by_rating_label' => 'Filter by rating',
            'sort_label' => 'Sort by',
            'sort_most_recent' => 'Most Recent',
            'sort_highest_rated' => 'Highest Rated',
            'sort_lowest_rated' => 'Lowest Rated',
            'claim_now_button' => 'Claim now',
            'highlighted_reviews_heading' => 'Highlighted Reviews',
            'recent_discussions_heading' => 'Recent Discussions',
            'go_to_reviews_link' => 'Go to reviews',
            'go_to_discussions_link' => 'Go to discussions',
            'free_trial_available_heading' => 'Free Trial Available',
            'pricing_per_prefix' => 'per',
            'community_sentiment_heading' => 'Community Sentiment',
            'no_comparisons_available' => 'No comparisons available yet.',
            'no_reviews_found' => 'No reviews found.',
            'view_original_translation' => 'View original language',

            // Product Profile Navigation Tabs & Sidebar
            'tab_description' => 'Description',
            'tab_alternatives' => 'Alternatives',
            'tab_faqs' => 'FAQs',
            'tab_compare' => 'Compare',
            'tab_reviews' => 'Reviews',
            'tab_discussions' => 'Discussions',
            'more_about_business_heading' => 'More about',

            // Comparison Matrix & Comparison Tool Text
            'your_comparison_heading' => 'Your Comparison',
            'search_product_to_compare_placeholder' => 'Search product to compare...',
            'all_products_already_added' => 'All available products have already been added to comparison.',
            'no_other_products_in_category' => 'No other products found in this category to compare.',
            'detailed_user_ratings_heading' => 'Detailed User Ratings',
            'no_criteria_available' => 'No rating criteria available yet.',
            'add_to_compare_button' => 'Add to compare',
            'no_description_available' => 'No description available yet.',
            'no_pros_added_yet' => 'No pros added yet.',
            'no_cons_added_yet' => 'No cons added yet.',
            'starting_price_label' => 'Starting price',
            'contact_pricing_label' => 'Contact for pricing',
            'custom_subscription_label' => 'Custom Subscription',
            'view_pricing_link' => 'View pricing plans',
            'free_trial_label' => 'Free trial',
            'available_label' => 'Available',
            'popular_comparisons_heading' => 'Popular Comparisons',
            'last_updated_prefix' => 'Last updated',

            // Category, Top-Rated & Alternatives Listings Text
            'all_filter_option' => 'All',
            'real_ratings_badge' => 'Real Ratings',
            'selected_features_label' => 'Selected Features',
            'clear_features_button' => 'Clear all',
            'no_matches_found' => 'No matches found',
            'no_pros_available' => 'No pros mentioned yet.',
            'no_cons_available' => 'No cons mentioned yet.',
            'overall_rating_label' => 'Overall rating',
            'recommend_label' => 'Recommend',
            'most_mentioned_pros_cons_subtext' => 'Most mentioned pros and cons shared by real users.',
            'most_mentioned_pro_label' => 'Most mentioned pro',
            'most_mentioned_con_label' => 'Most mentioned con',
            'key_pro_label' => 'Key pro',
            'key_con_label' => 'Key con',
            'current_listing_badge' => 'Current',
            'no_businesses_found_search' => 'No businesses found matching your search.',

            // Community Discussions Text
            'active_today_label' => 'active today',
            'sort_top_popular' => 'Top / Popular',
            'sort_most_recent_disc' => 'Most Recent',
            'sort_most_active_disc' => 'Most Active',
            'pinned_discussion_badge' => 'Pinned',
            'what_are_your_thoughts_placeholder' => 'What are your thoughts on this business?',
            'want_to_join_discussion_prompt' => 'Want to join the discussion?',
            'search_discussions_placeholder' => 'Search discussions...',
            'discussion_title_field_label' => 'Title / Question',
            'discussion_title_placeholder' => 'What would you like to ask or share?',
            'discussion_details_field_label' => 'Details & Context',
            'discussion_details_placeholder' => 'Provide background context, specific questions, or personal experience...',
            'no_discussions_match' => 'No discussions match your search query.',
            'join_conversation_title' => 'Join the conversation',
            'join_conversation_desc' => 'Connect with other users to ask questions, share experiences, and exchange useful tips.',
            'start_discussion_button' => 'Start a discussion',

            // Add Review Form & Modal Text
            'recommend_yes_option' => 'Yes, I recommend',
            'recommend_no_option' => 'No, I do not recommend',
            'rating_required_error' => 'Please provide a rating before submitting.',
            'review_title_placeholder' => 'Summarize your experience in a title',
            'review_text_placeholder' => 'Share details about your overall experience...',
            'review_pros_subtext' => 'What did you like best?',
            'review_cons_subtext' => 'What could be improved?',
            'search_pros_placeholder' => 'Search or add a pro...',
            'search_cons_placeholder' => 'Search or add a con...',
            'do_you_recommend_question' => 'Do you recommend this business?',
            'rating_guideline_1' => 'Be honest and direct',
            'rating_guideline_2' => 'Describe your real-world experience',
            'rating_guideline_3' => 'Highlight specific pros and cons',
            'rating_guideline_4' => 'Avoid inappropriate or promotional content',

            // Report Modals & FAQ Feedback Text
            'report_a_review_heading' => 'Report a Review',
            'select_issue_reason_subtext' => 'Please select the reason for reporting this review:',
            'additional_details_optional' => 'Additional Details (Optional)',
            'additional_details_placeholder' => 'Provide any additional context regarding this report...',
            'cancel_button' => 'Cancel',
            'submit_report_button' => 'Submit Report',
            'was_this_helpful_question' => 'Was this helpful?',
            'helpful_yes' => 'Yes',
            'helpful_no' => 'No',
            'was_this_answer_helpful' => 'Was this answer helpful?',
            'still_have_questions_prompt' => 'Still have questions?',
            'ask_community_button' => 'Ask the Community',

            // Write Review Landing Page Text
            'write_review_hero_title' => 'Share Your Experience',
            'write_review_hero_subtitle' => 'Help millions of buyers choose the right products and services.',
            'write_review_search_placeholder' => 'Search by business name or website...',
            'popular_with_community_title' => 'Popular with the Community',
            'popular_with_community_desc' => 'Businesses and tools actively reviewed by users this month.',
            'be_first_to_review_title' => 'Be the First to Review',
            'be_first_to_review_desc' => 'Help others by sharing the first review on newly added businesses.',
            'recently_reviewed_title' => 'Recently Reviewed',
            'recently_reviewed_desc' => 'See the latest verified user feedback submitted to Localio.',
            'review_card_button' => 'Write a review',

            // Global Layout, Auth Modal & Share Modal Text
            'choose_country_region_heading' => 'Choose your Country/Region',
            'disclosure_label' => 'Advertiser Disclosure',
            'community_guidelines_link_text' => 'Community Guidelines',
            'sign_in_or_create_account_title' => 'Sign in or create account',
            'continue_with_google_button' => 'Continue with Google',
            'continue_button' => 'Continue',
            'create_your_profile_title' => 'Create your profile',
            'first_name_placeholder' => 'First name',
            'last_name_placeholder' => 'Last name',
            'job_title_placeholder' => 'Job title',
            'company_size_dropdown_label' => 'Company size',
            'categories_nav_label' => 'Categories',
            'share_this_heading' => 'Share this',
            'copy_link_button' => 'Copy link',
            'copied_toast' => 'Link copied to clipboard!',
            'all_rights_reserved_text' => 'All rights reserved.',

            // Error, Validation & Notification Messages
            'error_auth_invalid_credentials' => 'Invalid email or password. Please try again.',
            'error_auth_account_deactivated' => 'Your account has been deactivated. Please contact support.',
            'error_auth_session_expired' => 'Your session has expired. Please sign in again.',
            'error_auth_user_not_found' => 'No user found with the provided email address.',
            'error_auth_otp_invalid' => 'The verification code entered is invalid or has expired.',
            'error_auth_failed' => 'Authentication failed. Please check your credentials.',
            'error_auth_social_failed' => 'Social login failed. Please try signing in with email.',
            'error_auth_registration_failed' => 'Registration could not be completed. Please check your input.',
            'error_login_required' => 'Please sign in to access this feature.',
            'error_password_current_incorrect' => 'The current password you provided is incorrect.',
            'error_password_same_as_current' => 'New password cannot be the same as your current password.',
            'error_password_min_length' => 'Password must be at least 8 characters long.',
            'error_review_login_required' => 'You must be logged in to submit a review.',
            'error_review_recommend_required' => 'Please indicate whether you recommend this business.',
            'error_review_submit_failed' => 'Failed to submit your review. Please try again.',
            'error_review_not_found' => 'The requested review could not be found.',
            'error_discussion_login_post' => 'Please sign in to post a new discussion.',
            'error_discussion_login_comment' => 'Please sign in to post a reply.',
            'error_discussion_login_vote' => 'Please sign in to vote.',
            'error_discussion_login_pin' => 'You do not have permission to pin discussions.',
            'error_permission_denied' => 'You do not have permission to perform this action.',
            'msg_discussion_posted_success' => 'Discussion posted successfully!',
            'msg_comment_posted_success' => 'Your comment has been posted successfully.',
            'msg_discussion_deleted_success' => 'Discussion deleted successfully.',
            'msg_comment_deleted_success' => 'Comment deleted successfully.',
            'error_compare_max_limit' => 'You can compare a maximum of 4 products at a time.',
            'error_product_not_found' => 'The requested product could not be found.',
            'error_report_login_issue' => 'Please sign in to submit a report.',
            'error_report_login_review' => 'Please sign in to report this review.',
            'msg_report_submitted_success' => 'Report submitted successfully. Our team will review it shortly.',
            'msg_review_report_success' => 'Review report submitted successfully.',
            'error_country_not_found' => 'The requested country or region could not be found.',
            'error_general_something_went_wrong' => 'Something went wrong. Please try again later.',
            'error_translation_unavailable' => 'Translation is currently unavailable for this content.',
            'error_connection_issue' => 'Unable to connect to the server. Please check your internet connection.',
        ];

        foreach ($keys as $k => $defaultValue) {
            $existing = StaticContentKey::where('key', $k)->first();
            $fieldType = (str_contains($k, '_des') || str_contains($k, 'mail_below_text') || str_contains($k, '_desc') || str_contains($k, '_message') || str_contains($k, '_text') || str_contains($k, '_subtext') || str_contains($k, '_guideline') || str_contains($k, '_error') || str_contains($k, 'error_') || str_contains($k, 'msg_')) ? 'textarea' : 'text';

            if (!$existing) {
                StaticContentKey::create([
                    'key' => $k,
                    'name' => ucwords(str_replace('_', ' ', $k)),
                    'default_value' => $defaultValue,
                    'field_type' => $fieldType,
                ]);
            } elseif (empty(trim($existing->default_value ?? ''))) {
                $existing->update([
                    'default_value' => $defaultValue,
                ]);
            }
        }

        Cache::forget('static_texts_all');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive rollback needed
    }
};
