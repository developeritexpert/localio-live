<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class HowItWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('how_it_works')->truncate();

        DB::table('how_it_works')->insert([
            [
                'lang_id' => 1,

                // Banner Section
                'banner_title' => 'How It Works',
                'banner_description' => 'Explore thousands of software solutions in different categories—from productivity tools to accounting systems.',
                'banner_image_left' => 'images/how-it-works/banner_left.jpg',
                'banner_image_right' => 'images/how-it-works/banner_right.jpg',

                // Main heading
                'main_heading' => 'How It Works',

                // Section 1 (best software section, skipped as static placeholder)
                'section_1_title' => 'Find the Best Software in Just a Few Clicks',
                'section_1_description' => 'We help you discover, compare, and choose the right software—based on real reviews and global insights',

                // Section 2 – CTA
                'section_2_title' => 'Ready to Get Started?',
                'section_2_description' => 'Find the right software faster, compare options and connect with top vendors now.',
                'section_2_button' => 'Get Started',

                // Section 3 – Why Trust Us
                'section_3_title' => 'Why Trust Us',
                'section_3_description' => 'Our platform is trusted by hundreds of users and businesses worldwide. We help you discover the best solutions in just a few clicks.',
                'section_3_image' => 'images/how-it-works/why-trust-us.jpg',


                'meta_title' => 'How It Works for Vendors',
                'meta_description' => 'Step-by-step guide on how vendors can find, compare and choose the right automotive software.',


                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
