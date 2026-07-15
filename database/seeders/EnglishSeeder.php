<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use App\Models\FooterContent;
use App\Models\HeaderContent;
use App\Models\HomeContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnglishSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('home_contents')->where('lang_id', 1)->delete();
DB::table('header_contents')->where('lang_id', 1)->delete();
DB::table('footer_contents')->where('lang_id', 1)->delete();
        $arr = [
            'header_title' => 'Find the Best Deals and Save on Your Next Purchase!',
            'header_description' => 'Get free, unbiased product comparisons, read real customer reviews, and',
            'header_background_img' => 'front/img/bnnr-bg.png',
            'header_img' => 'front/img/banner_image.png',
            'placeholder_text' => 'Search for a company or category...',
            'trusted_brands_text' => 'Trusted Brands, Unbeatable Choices',
            'trusted_brands_img' => 'front/img/marq-img1.svg',
            'most_popular' => 'Most Popular',
            'campare_business' => 'Compare Business Software',
            'visit_website' => 'visit website',
            'exclusive_deals' => 'Exclusive deals',
            'all_exclusive' => 'All Exclusive deals',
            'get_this_deal' => 'Get This Deal',
            'ai_section_left_img' => 'front/img/right-tool-vector1.png',
            'ai_section_right_img' => 'front/img/right-tool-vector2.png',
            'ai_title' => 'AI-Powered Smart Search',
            'ai_description' => 'Quickly discover and compare the best products with our AI-powered search, designed to match your specific needs and preferences.',
            'ai_placeholder' => 'Search for a company or category...',
            'ai_send_img' => 'front/img/btn-img.svg',
            'top_product' => 'Top Rated Products',
            'all_top_product' => 'All Top-Rated Products',
            'latest_reviews' => 'Latest Reviews',
            'write_review' => 'Write a Review',
            'review_section_right_img' => 'front/img/right-tool-vector1.png',
            'review_section_left_img' => 'front/img/right-tool-vector2.png',
            'read_article' => 'Read Our Articles',
            'view_all_article' => 'View All Articles',
            'find_tool' => 'Find the Right Tool',
            'find_tool_left_img' => 'front/img/right-tool-vector1.png',
            'find_tool_right_img' => 'front/img/right-tool-vector2.png',
            'user_reviews_img' => 'front/img/right-tool-img1.png',
            'verify_user_review' => 'Verified User Reviews',
            'verify_review_description' => 'Read real feedback from verified users to help you make the right choice.',
            'price_compare_img' => 'front/img/right-tool-img2.png',
            'feature_price' => 'Feature and Price Comparisons',
            'feature_price_description' => 'Easily compare software based on key features, pricing, and customer ratings.',
            'independent_img' => 'front/img/right-tool-img3.png',
            'independent' => 'Independent Insights',
            'independent_description' => 'Access unbiased, data-driven research to get the most value from your software.',
            'get_button_lable' => 'Get Started'
        ];


        $headercontent = [
            'header_logo' => 'front/img/logo.svg',
            'header_search_placeholder' => 'Search for a company or category...',
            'login_btn_lable' => 'Log In',
            'sign_up_btn_lable' => 'Sign Up',
            'sign_out_btn_lable' => 'Sign Out',
            'exclusive' => 'Exclusive',
            'categories' => 'Categories',
            'top_rated_product' => 'Top-rated product',
            'expert_guide' => 'Expert Guide',
            'help_center' => 'Help Center',
        ];

        $footercontent = [
            'footer_logo' => 'front/img/logo.svg',
            'facebook_icon' => 'header_logo/1734508523_facebook.svg',
            'instagram_icon' => 'header_logo/1734508559_Instagram.svg',
            'twitter_icon' => 'header_logo/1734508611_Twitter.svg',
            'facebook_url' => 'https://www.facebook.com/login/',
            'twitter_url' => 'https://www.instagram.com/accounts/login/',
            'discover' => 'https://x.com/i/flow/login',
            'categories' => 'Discover',
            'top_rated_product' => 'Top-rated products',
            'exclusive_deal' => 'Exclusive deals',
            'company' => 'Company',
            'who_we_are' => 'Who we are',
            'privacy_policy' => 'Privacy policy',
            'terms_and_conditions' => 'Terms and conditions',
            'vendors' => 'Vendors',
            'get_listed' => 'Sign up',
            'vendor_login' => 'Vendor login',
            'help' => 'Help',
            'expert_guides' => 'Expert guides',
            'help_center' => 'Help Center',
            'contact' => 'Contact',
            'follow_us' => 'Follow us',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'twitter' => 'Twitter',
        ];

        foreach($arr as $meta_key => $meta_value){
            HomeContent::create([
                'meta_key'  => $meta_key,
                'meta_value' => $meta_value,
                'lang_id' => '1'
            ]);
        }

        foreach($headercontent as $meta_key=> $meta_value){
            HeaderContent::create([
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'lang_id' => '1'
            ]);
        }

        foreach($footercontent as $meta_key=> $meta_value){
            FooterContent::create([
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'lang_id' => '1'
            ]);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
