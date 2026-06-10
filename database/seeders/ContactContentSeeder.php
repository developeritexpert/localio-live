<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('contact_contents')->truncate();

        DB::table('contact_contents')->insert([
            'contact_heading' => 'Contact us',
            'image_first' => 'public/front/img/20250203132618_image_first.png',
            'image_second' => 'public/front/img/20250203132713_image_second.png',
            'footer_heading' => 'We are here to help!',
            'g_button' => 'Contact Us',
            'meta_title'=>'Contact Page',
            'meta_description'=>'here is contact page',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
