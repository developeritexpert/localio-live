<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CategoryPageContent extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('category_page_contents')->truncate();  // Truncate the table
        DB::table('category_page_contents')->insert([

            [
                'meta_key'  =>  'header_image',
                'meta_value'    =>  'front/img/ctgry-bannr.png',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
                'type'=> 'text',
            ],
            [
                'meta_key'  =>  'meta_title',
                'meta_value'    =>  'Browse Our Software Categories',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
                'type'=> 'text',
            ],
            [
                'meta_key'  =>  'meta_discription',
                'meta_value'    =>  'All Categories',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
                'type'=> 'text',
            ],
            [
                'meta_key'  =>  'header_bg_image',
                'meta_value'    =>  'front/img/small-bnnr-bg.png',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
                'type'=> 'text',
            ],
            [
                'meta_key'  =>  'heading',
                'meta_value'    =>  'Browse Our Software Categories',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
                'type'=> 'text',
            ],
            [
                'meta_key'  =>  'description',
                'meta_value'    =>  'Find your software in one of our 900+ categories. From Accounting to Yoga Studio Management, we cover it all!',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
                'type'=> 'text',

            ],
            [
                'meta_key'  =>  'search_placeholder_text',
                'meta_value'    =>  'Search for a company or category...',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
                'type'=> 'text',

            ],
            [
                'meta_key'  =>  'main_heading',
                'meta_value'    =>  'What type of software are you looking for?',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
                'type'=> 'text',

            ]
            ]);
    }
}
