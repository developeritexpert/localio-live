<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class TopProductContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('top_product_contents')->truncate();

        DB::Table('top_product_contents')->insert([
            [
                'meta_key' => 'banner_image',
                'meta_value' => 'front/img/top-rated-bnr-bg.png',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ],
            [
                'meta_key' => 'banner_bg_image',
                'meta_value' => 'front/img/small-bnnr-bg.png',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'facebook_icon',
                'meta_value' => 'header_logo/1734611650_Facebook.svg',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'pinterest_icon',
                'meta_value' => 'header_logo/1734611691_Pinterest.svg',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'twitter_icon',
                'meta_value' => 'header_logo/1734611712_Twitter.svg',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'copylink_icon',
                'meta_value' => 'header_logo/1734611735_Link.svg',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'more_icon',
                'meta_value' => 'header_logo/1734611756_More.svg',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'mail_image',
                'meta_value' => 'header_logo/1734611997_subs.png',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ],
            [
                'meta_key' => 'green_tick_img',
                'meta_value' => 'front/img/tick-img.png',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],

            // text section
            [
                'meta_key' => 'header_title',
                'meta_value' => 'Automotive',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ],
            [
                'meta_key' => 'header_sub_title',
                'meta_value' => 'How to find the right Automotive Software',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ],
            [
                'meta_key' => 'header_bottom_text',
                'meta_value' => 'Localio provides independent research and reviews. We may earn affiliate commissions.',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'learn_more',
                'meta_value' => 'Learn more',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'search_placeholder',
                'meta_value' => 'Search product name',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],

            [
                'meta_key' => 'user_rating',
                'meta_value' => 'User Rating',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ],
            [
                'meta_key' => 'price',
                'meta_value' => 'Price',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ],
            [
                'meta_key' => 'price_option',
                'meta_value' => 'Pricing Options',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'features',
                'meta_value' => 'Features',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ],
            [
                'meta_key' => 'show_more',
                'meta_value' => 'Show more ',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'deployment',
                'meta_value' => 'Deployment',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],

            [
                'meta_key' => 'company_size',
                'meta_value' => 'Company Size',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'drop_down_text',
                'meta_value' => 'Top Rated Products',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'visit_website',
                'meta_value' => 'Visit Website',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'read_more',
                'meta_value' => 'Read More',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],

            [
                'meta_key' => 'key_features',
                'meta_value' => 'Key Features',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'starting_price',
                'meta_value' => 'Starting Price',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'month',
                'meta_value' => 'Month',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'compare_products',
                'meta_value' => 'Compare Products',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'rating',
                'meta_value' => 'ratings',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'footer_title',
                'meta_value' => ' Top-rated Products of',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => "footer_sub_title",
                'meta_value' => "Fill out the form and we'll send a list of the top-rated﻿ software based on real user reviews directly to your inbox.",
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'email_placeholder',
                'meta_value' => 'Email Address*',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'subscribe_lable',
                'meta_value' => 'Subscribe',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'you_agree',
                'meta_value' => 'By proceeding, you agree to our',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'terms_of_use',
                'meta_value' => 'Terms Of Use',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'privacy_policy',
                'meta_value' => 'Privacy Policy.',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],
            [
                'meta_key' => 'and',
                'meta_value' => 'and',
                'lang_id'    =>   '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),


            ],

        ]);
    }
}
