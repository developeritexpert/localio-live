<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class BusinessPromptAttachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('ai_prompt_attaches')->truncate();

        DB::table('ai_prompt_attaches')->insert([
            [
               'resource_id'=>1000,
               'frontend_img_path'=>'/assets/prompt-box-images/prompt_box_1.png',
               'backend_img_path'=>null,
               'page_type'=>'business',
            ],

            [
                'resource_id'=>1002,
                'frontend_img_path'=>'/assets/prompt-box-images/prompt_box_2.png',
                'backend_img_path'=>null,
                'page_type'=>'business',
             ],

             [
                'resource_id'=>1003,
                'frontend_img_path'=>'/assets/prompt-box-images/prompt_box_3.png',
                'backend_img_path'=>null,
                'page_type'=>'business',
             ],
           
        ]);
    }
}
