<?php

namespace App\Http\Controllers\Admin\SiteContent;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\LearnMoreContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\StaticContentKey;
use App\Models\StaticContentTranslation;
use Illuminate\Support\Str;

class StaticContentController extends Controller
{
    public function modalindex()
    {
        $langId = getCurrentLanguageID();

        $sections = LearnMoreContent::where('lang_id', $langId)
        ->orderBy('sort_order')
        ->get();

        return view('Admin.learn-more-modal.index', compact('sections'));
    }
    public function modalcreate()
    {
        return view('Admin.learn-more-modal.add',);
    }
    public function edit($id)
    {
        $langId = getCurrentLanguageID();

        $section = LearnMoreContent::where('lang_id', $langId)
            ->findOrFail($id);
        return view('Admin.learn-more-modal.add', compact('section',  'langId'));
    }
    public function modalstoreOrUpdate(Request $request)
    {
        $langId = getCurrentLanguageID();

        // Basic validation first (required, max length)
        $request->validate([
            'sections' => 'required|array',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.content' => 'required|string',
        ]);
        foreach ($request->input('sections') as $index => $sectionData) {
            if (empty($sectionData['title'])) continue;

            $query = LearnMoreContent::where('lang_id', $langId)
                ->where('title', $sectionData['title']);

            if (!empty($sectionData['id'])) {
                $query->where('id', '!=', $sectionData['id']);
            }

            if ($query->exists()) {
                return redirect()->back()
                    ->withErrors(["sections.{$index}.title" => "The title '{$sectionData['title']}' must be unique for this language."])
                    ->withInput();
            }
        }

        // Check for duplicates within input array
        $titles = array_map(fn($s) => $s['title'], $request->input('sections'));
        if (count($titles) !== count(array_unique($titles))) {
            return redirect()->back()
                ->withErrors(['sections' => 'Titles must be unique within the input.'])
                ->withInput();
        }

        // Everything validated - proceed to DB operations
        DB::transaction(function () use ($request, $langId) {
            foreach ($request->input('sections') as $index => $sectionData) {
                if (empty($sectionData['title']) && empty($sectionData['content'])) {
                    continue;
                }

                if (isset($sectionData['id']) && !empty($sectionData['id'])) {
                    $content = LearnMoreContent::where('id', $sectionData['id'])
                        ->where('lang_id', $langId)
                        ->first();

                    if ($content) {
                        $content->update([
                            'title' => $sectionData['title'],
                            'content' => $sectionData['content'],
                            // 'sort_order' => $index + 1,
                            'section_type' => 'body',
                        ]);
                    } else {
                        // If id given but record not found for this lang, create new
                        $this->createLearnMoreContent($sectionData, $index, $langId);
                    }
                } else {
                    $this->createLearnMoreContent($sectionData, $index, $langId);
                }
            }
        });

        return redirect()->route('learn-modal')->with('success', 'Learn More content saved successfully.');
    }

    private function createLearnMoreContent($sectionData, $index, $langId)
    {
        return LearnMoreContent::create([
            'title' => $sectionData['title'],
            'content' => $sectionData['content'],
            'lang_id' => $langId,
            // 'sort_order' => $index + 1,
            'slug' => Str::slug($sectionData['title'] . '-' . time()),
            'section_type' => 'body',
        ]);
    }
    public function modaldestroy($id)
    {
        $content = LearnMoreContent::findOrFail($id);
        $content->delete();
        return back()->with('success', 'Section deleted successfully.');
    }

    public function index(Request $request)
    {
        $selectedLangCode = $request->get('lang');
        if ($selectedLangCode) {
            $selectedLang = Language::where('lang_code', $selectedLangCode)->first();
            $langId = $selectedLang ? $selectedLang->id : getCurrentLanguageID();
        } elseif ($request->has('lang_id')) {
            $langId = (int)$request->get('lang_id');
            $selectedLang = Language::find($langId);
        } else {
            $langId = getCurrentLanguageID();
            $selectedLang = Language::find($langId);
        }

        $langCode = $selectedLang ? $selectedLang->lang_code : 'en-us';
        $languages = Language::where('status', 1)->get();
        $defaultLangId = 1;

        $keys = StaticContentKey::with(['translations' => function ($q) use ($langId, $defaultLangId) {
            if ($langId !== $defaultLangId) {
                $q->where('lang_id', $langId);
            }
        }])->get()->keyBy('key');

        $sections = [
            'rating_labels' => [
                'title' => 'Rating Breakdown Word Labels & Thresholds',
                'keys' => [
                    'rating_label_excellent',
                    'rating_threshold_excellent',
                    'rating_label_great',
                    'rating_threshold_great',
                    'rating_label_good',
                    'rating_threshold_good',
                    'rating_label_satisfactory',
                    'rating_threshold_satisfactory',
                    'rating_label_poor',
                    'rating_threshold_poor',
                ],
            ],
            'company_size' => [
                'title' => 'Company Size Options',
                'keys' => [
                    'company_size_1',
                    'company_size_2',
                    'company_size_3',
                    'company_size_4',
                ],
            ],
            'community_card' => [
                'title' => 'Community Insights Box (Category & Top-Rated Header)',
                'keys' => [
                    'community_insights_title',
                    'community_insights_desc',
                    'how_rankings_work',
                    'join_community_heading',
                    'join_community_subheading',
                    'join_benefit_share_exp',
                    'join_benefit_discussions',
                    'join_benefit_reputation',
                ],
            ],
            'listing_badges' => [
                'title' => 'Listing Badges & Recommendation (Category & Top-Rated Cards)',
                'keys' => [
                    'category_badge_label',
                    'recommend_this',
                    'product_badge_label',
                ],
            ],
            'action_buttons' => [
                'title' => 'Card & Listing Action Buttons',
                'keys' => [
                    'profile_and_reviews',
                    'visit_website',
                    'get_started_button',
                ],
            ],
            'common' => [
                'title' => 'Common Page Text',
                'keys' => [
                    'compare_search_placeholder',
                    'latest_reviews',
                    'view_more',
                    'reviews_word',
                ],
            ],
            'filters' => [
                'title' => 'Filters Section Text (Category, Top-Rated, Alternatives)',
                'keys' => [
                    'filters',
                    'user_rating',
                    'price_range',
                    'reset_filter',
                    'no_prod_mach_fil',
                ],
            ],
            'auth' => [
                'title' => 'Authentication Text',
                'keys' => [
                    'login',
                    'sign_up',
                    'forgot_password',
                    'already_to_localio',
                    'register_to_localio',
                    'create_an_account',
                ],
            ],
            'product' => [
                'title' => 'Product Section Text',
                'keys' => [
                    'exclusive_deals_title',
                    'exclusive_deals_des',
                    'product_search_placeholder',
                    'localio_commissions_message',
                    'vs_keyword',
                    'business_reviews_subheadline',
                    'business_faqs_subheadline',
                    'business_comparisons_subheadline',
                    'compare_with_any_provider_desc',
                    'business_alternatives_subheadline',
                    'alternatives_page_most_popular_headline',
                    'alternatives_page_most_popular_desc',
                ],
            ],
            'help' => [
                'title' => 'Help & Support Text',
                'keys' => [
                    'help_search_bar_text',
                    'no_faq_avaliable',
                    'knowledge_base',
                    'knowledge_base_desc',
                    'no_policy',
                    'no_term',
                    'contact_us',
                    'send_message',
                ],
            ],
            'Mail' => [
                'title' => 'Mail text For Category And Top Rated Page',
                'keys' => [
                    'top_rated_mail_section_titile',
                    'top_rated_mail_section_desc',
                    'subscribe',
                    'mail_below_text',
                ],
            ],
            'review_success_modal' => [
                'title' => 'Review Success Modal',
                'keys' => [
                    'review_success_headline',
                    'review_success_text',
                    'review_success_close_btn',
                ],
            ],
            'product_detail_page' => [
                'title' => 'Product Profile & Listing Cards Text',
                'keys' => [
                    'review_breakdown_heading',
                    'recommended_by_users',
                    'pros_label',
                    'cons_label',
                    'submit_your_review_heading',
                    'write_a_review_placeholder',
                    'submit_review_button',
                    'share_experience_subheading',
                    'not_rated_yet',
                    'filter_by_rating_label',
                    'sort_label',
                    'sort_most_recent',
                    'sort_highest_rated',
                    'sort_lowest_rated',
                    'claim_now_button',
                    'highlighted_reviews_heading',
                    'recent_discussions_heading',
                    'go_to_reviews_link',
                    'go_to_discussions_link',
                    'free_trial_available_heading',
                    'pricing_per_prefix',
                    'community_sentiment_heading',
                    'no_comparisons_available',
                    'no_reviews_found',
                    'view_original_translation',
                ],
            ],
            'product_navigation_tabs' => [
                'title' => 'Product Profile Navigation Tabs & Sidebar',
                'keys' => [
                    'tab_description',
                    'tab_alternatives',
                    'tab_faqs',
                    'tab_compare',
                    'tab_reviews',
                    'tab_discussions',
                    'more_about_business_heading',
                ],
            ],
            'comparison_matrix' => [
                'title' => 'Comparison Matrix & Comparison Tool Text',
                'keys' => [
                    'your_comparison_heading',
                    'search_product_to_compare_placeholder',
                    'all_products_already_added',
                    'no_other_products_in_category',
                    'detailed_user_ratings_heading',
                    'no_criteria_available',
                    'add_to_compare_button',
                    'no_description_available',
                    'no_pros_added_yet',
                    'no_cons_added_yet',
                    'starting_price_label',
                    'contact_pricing_label',
                    'custom_subscription_label',
                    'view_pricing_link',
                    'free_trial_label',
                    'available_label',
                    'popular_comparisons_heading',
                    'last_updated_prefix',
                ],
            ],
            'category_listings' => [
                'title' => 'Category, Top-Rated & Alternatives Listings Text',
                'keys' => [
                    'all_filter_option',
                    'real_ratings_badge',
                    'selected_features_label',
                    'clear_features_button',
                    'no_matches_found',
                    'no_pros_available',
                    'no_cons_available',
                    'overall_rating_label',
                    'recommend_label',
                    'most_mentioned_pros_cons_subtext',
                    'most_mentioned_pro_label',
                    'most_mentioned_con_label',
                    'current_listing_badge',
                    'no_businesses_found_search',
                ],
            ],
            'community_discussions' => [
                'title' => 'Community Discussions Text',
                'keys' => [
                    'active_today_label',
                    'sort_top_popular',
                    'sort_most_recent_disc',
                    'sort_most_active_disc',
                    'pinned_discussion_badge',
                    'what_are_your_thoughts_placeholder',
                    'want_to_join_discussion_prompt',
                    'search_discussions_placeholder',
                    'discussion_title_field_label',
                    'discussion_title_placeholder',
                    'discussion_details_field_label',
                    'discussion_details_placeholder',
                    'no_discussions_match',
                ],
            ],
            'review_form_modal' => [
                'title' => 'Add Review Form & Modal Text',
                'keys' => [
                    'recommend_yes_option',
                    'recommend_no_option',
                    'rating_required_error',
                    'review_title_placeholder',
                    'review_text_placeholder',
                    'review_pros_subtext',
                    'review_cons_subtext',
                    'search_pros_placeholder',
                    'search_cons_placeholder',
                    'do_you_recommend_question',
                    'rating_guideline_1',
                    'rating_guideline_2',
                    'rating_guideline_3',
                    'rating_guideline_4',
                ],
            ],
            'report_feedback_modals' => [
                'title' => 'Report Modals & FAQ Feedback Text',
                'keys' => [
                    'report_a_review_heading',
                    'select_issue_reason_subtext',
                    'additional_details_optional',
                    'additional_details_placeholder',
                    'cancel_button',
                    'submit_report_button',
                    'was_this_helpful_question',
                    'helpful_yes',
                    'helpful_no',
                    'was_this_answer_helpful',
                    'still_have_questions_prompt',
                    'ask_community_button',
                ],
            ],
            'write_review_landing' => [
                'title' => 'Write Review Landing Page Text',
                'keys' => [
                    'write_review_hero_title',
                    'write_review_hero_subtitle',
                    'write_review_search_placeholder',
                    'popular_with_community_title',
                    'popular_with_community_desc',
                    'be_first_to_review_title',
                    'be_first_to_review_desc',
                    'recently_reviewed_title',
                    'recently_reviewed_desc',
                    'review_card_button',
                ],
            ],
            'global_layout_modals' => [
                'title' => 'Global Layout, Auth Modal & Share Modal Text',
                'keys' => [
                    'choose_country_region_heading',
                    'disclosure_label',
                    'community_guidelines_link_text',
                    'sign_in_or_create_account_title',
                    'continue_with_google_button',
                    'continue_button',
                    'create_your_profile_title',
                    'first_name_placeholder',
                    'last_name_placeholder',
                    'job_title_placeholder',
                    'company_size_dropdown_label',
                    'categories_nav_label',
                    'share_this_heading',
                    'copy_link_button',
                    'copied_toast',
                    'all_rights_reserved_text',
                ],
            ],
            'error_messages' => [
                'title' => 'Error, Validation & Notification Messages',
                'keys' => [
                    'error_auth_invalid_credentials',
                    'error_auth_account_deactivated',
                    'error_auth_session_expired',
                    'error_auth_user_not_found',
                    'error_auth_otp_invalid',
                    'error_auth_failed',
                    'error_auth_social_failed',
                    'error_auth_registration_failed',
                    'error_login_required',
                    'error_password_current_incorrect',
                    'error_password_same_as_current',
                    'error_password_min_length',
                    'error_review_login_required',
                    'error_review_recommend_required',
                    'error_review_submit_failed',
                    'error_review_not_found',
                    'error_discussion_login_post',
                    'error_discussion_login_comment',
                    'error_discussion_login_vote',
                    'error_discussion_login_pin',
                    'error_permission_denied',
                    'msg_discussion_posted_success',
                    'msg_comment_posted_success',
                    'msg_discussion_deleted_success',
                    'msg_comment_deleted_success',
                    'error_compare_max_limit',
                    'error_product_not_found',
                    'error_report_login_issue',
                    'error_report_login_review',
                    'msg_report_submitted_success',
                    'msg_review_report_success',
                    'error_country_not_found',
                    'error_general_something_went_wrong',
                    'error_translation_unavailable',
                    'error_connection_issue',
                ],
            ],
        ];
        $currentLanguage = Language::find($langId);
        return view('Admin.site-content.site_content', compact('keys', 'sections', 'langId', 'langCode', 'languages', 'currentLanguage'));
    }

    public function untranslatedIndex(Request $request)
    {
        $langId = $request->input('lang_id') ? (int)$request->input('lang_id') : getCurrentLanguageID();
        $langCode = $request->input('lang_code') ?? getCurrentLocale();
        $languages = Language::all();

        $allKeys = StaticContentKey::with(['translations' => function ($q) use ($langId) {
            $q->where('lang_id', $langId);
        }])->get();

        $keys = $allKeys->keyBy('key');

        $sections = [
            'dynamic_variables' => [
                'title' => 'Global Dynamic Variables (For Categories)',
                'keys' => [
                    'country1',
                    'country2',
                ],
            ],
        ];

        $currentLanguage = Language::find($langId);
        return view('Admin.site-content.untranslated_content', compact('keys', 'sections', 'langId', 'langCode', 'languages', 'currentLanguage'));
    }

    public function update(Request $request)
    {
        $texts = $request->input('texts', []);
        $slugs = $request->input('slugs', []);
        $langId = $request->input('lang_id') ? (int)$request->input('lang_id') : getCurrentLanguageID();
        $defaultLangId = 1;

        DB::beginTransaction();
        try {
            // Update URL slugs on Language model if provided
            if (!empty($slugs)) {
                $langModel = Language::find($langId);
                if ($langModel) {
                    if (isset($slugs['faq_slug'])) $langModel->faq_slug = Str::slug($slugs['faq_slug']) ?: 'faqs';
                    if (isset($slugs['alternatives_slug'])) $langModel->alternatives_slug = Str::slug($slugs['alternatives_slug']) ?: 'alternatives';
                    if (isset($slugs['reviews_slug'])) $langModel->reviews_slug = Str::slug($slugs['reviews_slug']) ?: 'reviews';
                    if (isset($slugs['comparisons_slug'])) $langModel->comparisons_slug = Str::slug($slugs['comparisons_slug']) ?: 'comparisons';
                    $langModel->save();
                }
            }
            foreach ($texts as $key => $value) {
                $keyModel = StaticContentKey::firstOrCreate(['key' => $key]);

                if ($langId == $defaultLangId) {
                    $keyModel->default_value = $value;
                    $keyModel->save();
                } else {
                    StaticContentTranslation::updateOrCreate(
                        ['static_content_key_id' => $keyModel->id, 'lang_id' => $langId],
                        ['value' => $value]
                    );
                }
            }

            DB::commit();
            cache()->forget('static_texts_all');
            return back()->with('success', 'Text content updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }




}
