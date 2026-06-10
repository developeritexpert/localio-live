<?php

namespace Database\Seeders;

use App\Models\StaticContent;
use App\Models\StaticContentKey;
use App\Models\StaticContentTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks if needed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('static_content_translations')->truncate();
        DB::table('static_content_keys')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $texts = [
            ['key' => 'compare_with_alternative', 'lang_id' => 1, 'value' => 'Compare With A Popular Alternative'],
            ['key' => 'top_reviews', 'lang_id' => 1, 'value' => 'Top Reviews'],
            ['key' => 'latest_reviews', 'lang_id' => 1, 'value' => 'Latest Reviews'],
            ['key' => 'suggested_products', 'lang_id' => 1, 'value' => "Based on other buyer's searches, these are the products that could be a good fit for you."],
            ['key' => 'login_to_your_account', 'lang_id' =>1, 'value'=> 'Login To Your Account'],
            ['key' => 'login', 'lang_id' =>1, 'value'=> 'Log In'],
            ['key' => 'view_more', 'lang_id' =>1, 'value'=> 'View More'],
            ['key' => 'sign_up', 'lang_id' =>1, 'value'=> 'Sign Up'],
            ['key' => 'forgot_password', 'lang_id' =>1, 'value'=> 'Forgot Password?'],
            ['key' => 'already_have_an_account', 'lang_id' =>1, 'value'=> 'Already have an Account?'],
            ['key' => 'trending', 'lang_id' =>1,'value'=> 'Trending'],
            ['key' => 'welcome_back', 'lang_id' =>1, 'value'=> 'Welcome back! Please choose a login method'],
            ['key' => 'explore_products', 'lang_id' =>1, 'value'=> 'Explore Products'],
            ['key' => 'help_search_bar_text', 'lang_id' =>1, 'value'=> 'How we can help you?'],
            ['key' => 'no_faq_avaliable', 'lang_id' =>1, 'value'=> 'No FAQs Available'],
            ['key' => 'knowledge_base', 'lang_id' =>1, 'value'=> 'Knowledge Base'],
            ['key' => 'knowledge_base_desc', 'lang_id' =>1, 'value'=> 'Explore helpful insights and resources to guide you through every step of your journey.'],
            ['key' => 'top_rated_product_title', 'lang_id' =>1, 'value'=> 'Top Rated Product'],
            ['key' => 'top_rated_product_des', 'lang_id' =>1, 'value'=> 'How to find the Best Product'],
            ['key' => 'exclusive_deals_title', 'lang_id' =>1, 'value'=> 'Top Rated Product'],
            ['key' => 'exclusive_deals_des', 'lang_id' =>1, 'value'=> 'How to find the Best Product'],
            ['key' => 'localio_commissions_message', 'lang_id' =>1, 'value'=> 'Localio provides independent research and reviews, and may earn affiliate commissions.'],
            ['key' => 'learn_more', 'lang_id' =>1, 'value'=> 'Learn More'],
            ['key' => 'product_search_placeholder', 'lang_id' =>1, 'value'=> 'Search product name'],
            ['key' => 'filters', 'lang_id' =>1, 'value'=> 'Filters'],
            ['key' => 'user_rating', 'lang_id' =>1, 'value'=> 'User Rating'],
            ['key' => 'price_range', 'lang_id' =>1, 'value'=> 'Price Range'],
            ['key' => 'no_prod_mach_fil', 'lang_id' =>1, 'value'=> 'No products found matching your selected filters.'],
            ['key' => 'reset_filter', 'lang_id' =>1, 'value'=> 'Reset All Filters'],
            ['key' => 'no_policy', 'lang_id' =>1, 'value'=> 'No privacy policies available in this language.'],
            ['key' => 'no_term', 'lang_id' =>1, 'value'=> 'No Terms and Conditions available in this language.'],
            ['key' => 'contact_us', 'lang_id' =>1, 'value'=> 'Contact Us'],
            ['key' => 'send_message', 'lang_id' =>1, 'value'=> 'Send Message'],
            ['key' => 'register_to_localio', 'lang_id' =>1, 'value'=> 'Register to Localio'],
            ['key' => 'new_to_localio', 'lang_id' =>1, 'value'=> 'New to Localio? '],
            ['key' => 'create_an_account', 'lang_id' =>1, 'value'=> 'Create an account'],
            ['key' => 'already_to_localio', 'lang_id' =>1, 'value'=> 'Already have an Account?'],
            ['key' => 'top_rated_mail_section_titile', 'lang_id' =>1, 'value'=> 'Get the Best Picks in Your Inbox'],
            ['key' => 'top_rated_mail_section_desc', 'lang_id' =>1, 'value'=> 'Drop your email to receive trusted software picks, all recommended by actual users.'],
            ['key' => 'subscribe', 'lang_id' =>1, 'value'=> 'Subscribe'],
            ['key' => 'mail_below_text', 'lang_id' =>1, 'value'=> 'I agree to receive promotional emails from Localio and  accept the Privacy Policy and Terms and Conditions. I can unsubscribe at any time.'],
            [
                'key' => 'category_badge_label',
                'lang_id' => 1,
                'value' => 'Top Choice',
            ],
            [
                'key' => 'product_badge_label',
                'lang_id' => 1,
                'value' => 'Key Features',
            ],

            [
                'key' => 'faq_title',
                'lang_id' => 1,
                'value' => 'Frequently Asked Questions (FAQs)',
            ],
            [
                'key' => 'faq_description',
                'lang_id' => 1,
                'value' => 'Find quick answers to the most common questions about using Localio to discover, filter, and connect with the best local businesses and products.',
            ],

        ];

        $defaultLangId = 1;

        // foreach ($texts as $text) {
        //     // Create or update default content
        //     $contentKey = StaticContentKey::updateOrCreate(
        //         ['key' => $text['key']],
        //         $text['lang_id'] == $defaultLangId ? ['default_value' => $text['value']] : []
        //     );

        //     // Insert translation only for non-default languages
        //     if ($text['lang_id'] != $defaultLangId) {
        //         StaticContentTranslation::updateOrCreate(
        //             [
        //                 'static_content_key_id' => $contentKey->id,
        //                 'lang_id' => $text['lang_id']
        //             ],
        //             ['value' => $text['value']]
        //         );
        //     }
        // }


        foreach ($texts as $text) {
            $name = ucwords(str_replace('_', ' ', $text['key']));
            $fieldType = 'text'; // default, customize per key if needed

            // Create or update default content
            $contentKey = StaticContentKey::updateOrCreate(
                ['key' => $text['key']],
                $text['lang_id'] == $defaultLangId
                    ? ['default_value' => $text['value'], 'name' => $name, 'field_type' => $fieldType]
                    : ['name' => $name, 'field_type' => $fieldType]
            );

            // Insert translation only for non-default languages
            if ($text['lang_id'] != $defaultLangId) {
                StaticContentTranslation::updateOrCreate(
                    [
                        'static_content_key_id' => $contentKey->id,
                        'lang_id' => $text['lang_id']
                    ],
                    ['value' => $text['value']]
                );
            }
        }



    }
}
