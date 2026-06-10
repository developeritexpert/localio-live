<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('categories')->truncate();

        DB::table('categories')->insert([
            [

               'total_products'=>0,
               'total_reviews'=>0,
               'image'=>'NULL',
               'category_icon'=>'CategoryIcons/finance.svg',
               'status'=>1,
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [

               'total_products'=>0,
               'total_reviews'=>0,
               'image'=>'NULL',
               'category_icon'=>'CategoryIcons/food.svg',
               'status'=>1,
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [

               'total_products'=>0,
               'total_reviews'=>0,
               'image'=>'NULL',
               'category_icon'=>'CategoryIcons/insurance.svg',
               'status'=>1,
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [

               'total_products'=>0,
               'total_reviews'=>0,
               'image'=>'NULL',
               'category_icon'=>'CategoryIcons/9ygQEEBG8BhKKsdrRjTEJ12QhbYiNfaiYqquy0HC.svg',
               'status'=>1,
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}