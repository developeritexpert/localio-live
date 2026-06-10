<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use Illuminate\Support\Str;

class PolicysSeeder extends Seeder
{

    public function run(): void
    {

          // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Truncate the tables (example)
    DB::table('policies')->truncate();
    DB::table('policy_translataion')->truncate();

    // Enable foreign key checks again
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        DB::table('policies')->insert([
            [
                'lang_id' =>1,
                'type' =>'pp',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'lang_id' =>1,
                'type' =>'pp',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'lang_id' =>1,
                'type' =>'pp',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'lang_id' =>1,
                'type' =>'pp',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
            'lang_id' =>1,
            'type' =>'pp',
            'created_at'    =>  now(),
            'updated_at'    =>  now(),
            ]
        ]);


        DB::table('policy_translataion')->insert([
            [
                'lang_id' => 1,
                'policy_id' => 1,
                'title' => 'Privacy Policy',
                'description' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. ',
                'key' => 'privacy_policy',
                'status' => "active",
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'lang_id' => 1,
                'policy_id' =>2,
                'title' => 'Personal information we collect',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure ',
                'key' => 'terms_conditions',
                'status' => "active",
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'lang_id' => 1,
                'policy_id' => 3,
                'title' => 'How do we use your personal information?',
                'description' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt:',
                'key' => 'refund_policy',
                'status' => "active",
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'lang_id' => 1,
                'policy_id' => 4,
                'title' => 'Your Rights',
                'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop',
                'key' => 'shipping_policy',
                'status' => "active",
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
            [
                'lang_id' => 1,
                'policy_id' => 5,
                'title' => 'Embedded content from other websites',
                'description' => 'here are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks.',
                'key' => 'cookie_policy',
                'status' => "active",
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ],
        ]);

    }
}
