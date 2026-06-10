<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhoWeAreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('who_we_ares')->truncate();

        DB::table('who_we_ares')->insert([
            [
                'lang_id'     =>  1,
                'main_heading' => 'Who We Are',
                'sub_heading' => 'Learn more about who we are',
                'bg_top_img' => 'public/front/img/small-bnnr-bg.png',
                'top_left_section_img'=>'public/front/img/image (1).svg',
                'top_right_section_img' => 'public/front/img/image.svg',
                'mp_heading' => 'Most Popular',
                'mp_sub_heading' => 'Lorem Ipsum has been the industry s standard dummy text ever since the 1500s, when an unknown printer too',
                'top_card_title' => 'Lorem Ipsum has been the industry s standard dummy',
                'top_card_image' => 'public/front/img/accor-bdy-img.png',
                'top_card_desc' => 'Lorem Ipsum has been the industry s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book, It has survived not only five centuries, but also the leap into electronic typesetting.',
                'specialists_heading' => 'Get to know the specialists behind your success',
                'ss_heading' => 'We help software and service providers find their best customer',
                'ss_sub_desc' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'protfolio_btn' => 'View Portfolio',
                'status' => 1,
                'meta_title'=>'who we are page',
                'meta_description'=>'who we are page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
