<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomeContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('home_contents')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('home_contents')->insert([
            [
                'meta_key' => 'meta_home_title',
                'meta_value' => 'Here is meta title heading',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'Meta_home_description',
                'meta_value' => 'Here is meta title description Get free, unbiased product comparisons, read real customer reviews, and',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'meta_key' => 'header_title',
                'meta_value' => 'Find the Best Deals and Save on Your Next Purchase!',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'header_description',
                'meta_value' => 'Get free, unbiased product comparisons, read real customer reviews, and',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'header_background_img',
                'meta_value' => 'front/img/bnnr-bg.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'header_img',
                'meta_value' => 'front/img/banner_image.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'placeholder_text',
                'meta_value' => 'Search for a company or category...',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),


            ],
            [
                'meta_key' => 'trusted_brands_text',
                'meta_value' => 'Trusted Brands, Unbeatable Choices',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'trusted_brands_img',
                'meta_value' => 'front/img/marq-img1.svg',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'most_popular',
                'meta_value' => 'Most Popular',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'campare_business',
                'meta_value' => 'Compare Business Software',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),


            ],
            [
                'meta_key' => 'visit_website',
                'meta_value' => 'visit website',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),


            ],
            [
                'meta_key' => 'exclusive_deals',
                'meta_value' => 'Exclusive deals',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),


            ],
            [
                'meta_key' => 'all_exclusive',
                'meta_value' => 'All Exclusive deals',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),


            ],
            [
                'meta_key' => 'get_this_deal',
                'meta_value' => 'Get This Deal',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),


            ],
            [
                'meta_key' => 'ai_section_left_img',
                'meta_value' => 'front/img/right-tool-vector1.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),


            ],
            [
                'meta_key' => 'ai_section_right_img',
                'meta_value' => 'front/img/right-tool-vector2.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),


            ],
            [
                'meta_key' => 'ai_title',
                'meta_value' => 'AI-Powered Smart Search',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'ai_description',
                'meta_value' => 'Quickly discover and compare the best products with our AI-powered search, designed to match your specific needs and preferences.',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'ai_placeholder',
                'meta_value' => 'Search for a company or category...',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'ai_send_img',
                'meta_value' => 'front/img/btn-img.svg',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'top_product',
                'meta_value' => 'Top Rated Products',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'all_top_product',
                'meta_value' => 'All Top-Rated Products',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'latest_reviews',
                'meta_value' => 'Latest Reviews',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'write_review',
                'meta_value' => 'Write a Review',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'review_section_right_img',
                'meta_value' => 'front/img/right-tool-vector1.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'review_section_left_img',
                'meta_value' => 'front/img/right-tool-vector2.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'read_article',
                'meta_value' => 'Read Our Articles',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'view_all_article',
                'meta_value' => 'View All Articles',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'find_tool',
                'meta_value' => 'Find the Right Tool',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'find_tool_left_img',
                'meta_value' => 'front/img/right-tool-vector1.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'find_tool_right_img',
                'meta_value' => 'front/img/right-tool-vector2.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'user_reviews_img',
                'meta_value' => 'front/img/right-tool-img1.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'verify_user_review',
                'meta_value' => 'Verified User Reviews',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'verify_review_description',
                'meta_value' => 'Read real feedback from verified users to help you make the right choice.',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'price_compare_img',
                'meta_value' => 'front/img/right-tool-img2.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'feature_price',
                'meta_value' => 'Feature and Price Comparisons',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'feature_price_description',
                'meta_value' => 'Easily compare software based on key features, pricing, and customer ratings.',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'independent_img',
                'meta_value' => 'front/img/right-tool-img3.png',
                'type'         => 'file',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'independent',
                'meta_value' => 'Independent Insights',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'independent_description',
                'meta_value' => 'Access unbiased, data-driven research to get the most value from your software.',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meta_key' => 'get_button_lable',
                'meta_value' => 'Get Started',
                'type'         => 'text',
                'lang_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
