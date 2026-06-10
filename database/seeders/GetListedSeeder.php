<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class GetListedSeeder extends Seeder
{

    public function run(): void{

        DB::table('get_listed')->truncate();
        DB::table('get_listed')->insert([
            [
                'lang_id' => 1,
        
                // Banner section
                'banner_heading' => 'Get Listed',
                'banner_sub_heading' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.',
                'banner_button' => 'Claim Profile',
                'banner_image_left' => 'images/left_image.jpg',
                'banner_image_right' => 'images/right_image.jpg',
        
                // Section 1
                'section_1_image' => 'images/section_1.jpg',
                'section_1_title' => 'Why Getting Listed Matters',
                'section_1_description' => 'Getting your business listed is the first step toward building a strong online presence. A verified listing not only boosts your credibility but also opens up opportunities to connect with potential customers actively searching for services or products like yours. Whether you are a small business or a growing enterprise, a listing ensures your visibility in local and global markets.',

                // Section 2
                'section_2_title' => 'Stand Out from the Competition',
                'section_2_description' => 'In a crowded marketplace, standing out is essential. With a complete and engaging business profile, you can showcase what makes your brand unique. Add rich descriptions, attractive images, accurate service details, and customer reviews to leave a lasting impression. Our platform gives you the tools to enhance your digital identity and attract more relevant leads to your business.',
                'section_2_image' => 'images/section_2.jpg',

                // Section 3
                'section_3_title' => 'Promote Your Best Features',
                'section_3_description' => 'Don’t just be listed — be discovered. Highlight your best services, share compelling visuals, and keep your information up to date to maintain customer trust. From managing reviews to updating contact details and offering promotional content, you have full control over how your brand is presented. Take advantage of this space to drive traffic, generate leads, and turn visitors into loyal customers.',
                'section_3_image' => 'images/section_3.jpg',
                'section_3_button' => 'Get Started Now',
                
                // Claim Section (array encoded as JSON)
                'claim_section' => json_encode([
                    [
                        'title' => 'Verified Profile',
                        'description' => 'Earn customer trust by claiming and verifying your business profile. A verified badge indicates authenticity and builds confidence among users.',
                    ],
                    [
                        'title' => 'Boost Visibility',
                        'description' => 'Show up more often in relevant searches and appear to customers looking for services you offer. A complete profile helps you rank better in listings.',
                    ],
                    [
                        'title' => 'Take Control',
                        'description' => 'Edit business details, manage user reviews, upload images, and track engagement — all from a simple, intuitive dashboard built for your success.',
                    ]
                ]),
        
                // SEO
                'meta_title' => 'Get Listed – Maximize Your Online Presence',
                'meta_description' => 'Build trust, improve visibility, and grow your customer base by listing your business on our platform. Claim your profile now and take control.',
        
                'created_at' => now(),
                'updated_at' => now(),
            ],
]);
    }
}
