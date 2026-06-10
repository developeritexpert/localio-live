<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\HelpCenterContent;


class HelpCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('help_center_categories')->truncate(); // child
        DB::table('help_center_contents')->truncate();  // parent
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Insert Help Center Content
        $content = HelpCenterContent::create([
            'lang_id' => 1,
            'banner_headline' => "Welcome to Help Center",
            'banner_description' => "This is the banner description for",
            'banner_img'=>"front/img/small-bnnr-bg.png",
            'main_heading' => "How can we help you?",
            'left_section_title' => 'Frequently Asked Questions',
            'left_section_description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry',
            'faq_section_title' => "Frequently Asked Questions",
            'faq_section_description' => "Here are some common questions and answers.",
            'meta_title'=>'Help Center Page',
            'meta_description' => 'Here is Help Center page',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $now = now();
        $content->categories()->createMany([
            [
                'image' => 'front/img/hlp-img-1.svg',
                'title' => 'Getting Started',
                'description' => "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer.",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'image' => 'front/img/hlp-img-2.svg',
                'title' => 'Customization',
                'description' => "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer.",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'image' => 'front/img/hlp-img-3.svg',
                'title' => 'Knowledge Base',
                'description' => "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer.",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'image' => 'front/img/hlp-img-4.svg',
                'title' => 'Widget',
                'description' => "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer.",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'image' => 'front/img/hlp-img-5.svg',
                'title' => 'Guides',
                'description' => "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer.",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'image' => 'front/img/hlp-img-6.svg',
                'title' => 'Account & Team',
                'description' => "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer.",
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    } 
}
