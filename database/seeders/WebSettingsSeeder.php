<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('web_settings')->truncate();
       $values =  [
            [
                'name' => 'User Default Image',
            'key' => 'user_default_image',
            'value' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'type' => 'config',
            'model_ref' => null,
            'params' => null,
            'field_type' => 'file'
            ],
            [
                'name' => 'User Dashboard Default Page Image',
                'key' => 'user_dashboard_default_page_image',
                'value' => null ,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'type' => 'config',
                'model_ref' => null,
                'params' => null,
                'field_type' => 'file',
            ]
        ];
        DB::table('web_settings')->insert($values);
    }
}
