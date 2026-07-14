<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class HeaderContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('header_contents')->truncate();

        DB::Table('header_contents')->insert([
            [
                'meta_key'  =>  'header_logo',
                'meta_value'    =>  'front/img/logo.svg',
                'type'         => 'file',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ],
            [
                'meta_key' =>'favicon_icon',
                'meta_value'    =>'front/img/icon.svg',
                'type'         =>'file',
                'lang_id'     =>'1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key' =>'favicon_icon',
                'meta_value'    =>'front/img/icon.svg',
                'type'         =>'file',
                'lang_id'     =>'2',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key' =>'code_at_beginning_of_head_tag',
                'meta_value'    =>null,
                'type'         =>'textarea',
                'lang_id'     =>'1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key' =>'code_at_end_of_head_tag',
                'meta_value'    =>null,
                'type'         =>'textarea',
                'lang_id'     =>'1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key'  =>  'header_search_placeholder',
                'meta_value'    =>  'Search for a company or category...',
                'type'         => 'text',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key'  =>  'login_btn_lable',
                'meta_value'    =>  'Login',
                'type'         => 'text',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key'  =>  'sign_up_btn_lable',
                'meta_value'    =>  'Sign Up',
                'type'         => 'text',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key'  =>  'sign_out_btn_lable',
                'meta_value'    =>  'Sign Out',
                'type'         => 'text',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key'  =>  'exclusive',
                'meta_value'    =>  'Exclusive',
                'type'         => 'text',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key'  =>  'categories',
                'meta_value'    =>  'Categories',
                'type'         => 'text',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key'  =>  'top_rated_product',
                'meta_value'    =>  'Top Rated Product',
                'type'         => 'text',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key'  =>  'expert_guide',
                'meta_value'    =>  'Expert Guide',
                'type'         => 'text',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'meta_key'  =>  'help_center',
                'meta_value'    =>  'Help Center',
                'type'         => 'text',
                'lang_id'     =>  '1',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],

        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
