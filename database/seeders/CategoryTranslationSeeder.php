<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('category_translations')->truncate();
        DB::table('category_translations')->insert([
            [
              
                'category_id'=>1,
                'lang_id'=>2,
                'name'=>'Finance & Investing',
                'slug'=>'Finance',
                'description'=>'covers money management, wealth growth, and smart investment strategies. It includes budgeting, saving, and investing in assets like stocks, real estate, and businesses to build financial security and long-term success.',
                 'created_at'    =>  now(),
                 'updated_at'    =>  now(),
             ],   
             [
              'category_id'=>2,
                'lang_id'=>2,
                'name'=>'Food & Beverage',
                'slug'=>'Food',
                'description'=>' focuses on cuisine, drinks, and dining experiences. It covers everything from recipes and cooking to restaurants, food trends, and beverages, including coffee, cocktails, and more.',
                 'created_at'    =>  now(),
                 'updated_at'    =>  now(),
             ], 
             [
                'category_id'=>3,
                  'lang_id'=>2,
                  'name'=>'Insurance',
                  'slug'=>'Insurances',
                  'description'=>'  provides financial protection against risks like accidents, health issues, and property damage. It includes life, health, auto, and business insurance, ensuring security and peace of mind.',
                   'created_at'    =>  now(),
                   'updated_at'    =>  now(),
               ],
               [
                'category_id'=>4,
                  'lang_id'=>2,
                  'name'=>'Cryptocurrency',
                  'slug'=>'Crypto',
                  'description'=>' involves digital assets like Bitcoin and Ethereum, using blockchain technology for secure, decentralized transactions. It covers trading, investing, and the evolving crypto market.',
                   'created_at'    =>  now(),
                   'updated_at'    =>  now(),
               ]
        ]);
       DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
