<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $keys = [
            [
                'key' => 'company_size_1',
                'default_value' => 'Freelance / Solo',
                'name' => 'Company Size: Freelance / Solo',
                'field_type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_size_2',
                'default_value' => 'Small Business (1-50 emp.)',
                'name' => 'Company Size: Small Business (1-50 emp.)',
                'field_type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_size_3',
                'default_value' => 'Mid-Market (51-1000 emp.)',
                'name' => 'Company Size: Mid-Market (51-1000 emp.)',
                'field_type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_size_4',
                'default_value' => 'Enterprise (>1000 emp.)',
                'name' => 'Company Size: Enterprise (>1000 emp.)',
                'field_type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($keys as $keyData) {
            DB::table('static_content_keys')->updateOrInsert(
                ['key' => $keyData['key']],
                $keyData
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('static_content_keys')->whereIn('key', [
            'company_size_1',
            'company_size_2',
            'company_size_3',
            'company_size_4',
        ])->delete();
    }
};
