<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilterTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table("filter_types")->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $types = [
            ['name' => 'Dropdown', 'slug' => 'dropdown'],
            ['name' => 'Checkbox', 'slug' => 'checkbox'],
            ['name' => 'Radio Button', 'slug' => 'radio'],
            ['name' => 'Range Sliger', 'slug' => 'slider'],
            ['name' => 'Toggle', 'slug' => 'toggle'],
        ];

        foreach ($types as $type) {
            DB::table('filter_types')->updateOrInsert(
                ['slug' => $type['slug']],
                [
                    'name' => $type['name'],
                    'slug' => $type['slug'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
