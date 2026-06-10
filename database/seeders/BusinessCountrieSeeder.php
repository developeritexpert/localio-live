<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BusinessCountrieSeeder extends Seeder
{
    /**
     * Run the database seeds.     
     */
    public function run(): void
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');       
        DB::table('business_country')->truncate(); 

        $data = [];
        // Add rows for business_id = 12 (country_id = 1 to 31)
        for ($i = 1; $i <= 31; $i++) {
            $data[] = [
                'id' => 1076 + $i,
                'business_id' => 12,
                'country_id' => $i,
                'created_at' => null,
                'updated_at' => null,
            ];
        }

        // Add rows for business_id = 13 (country_id = 1 to 19)
        for ($i = 1; $i <= 19; $i++) {
            $data[] = [
                'id' => 1108 + ($i - 1),
                'business_id' => 13,
                'country_id' => $i,
                'created_at' => null,
                'updated_at' => null,
            ];
        }

        DB::table('business_country')->insert($data);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
   
    }
}
