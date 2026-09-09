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
                'key' => 'category_badge_label',
                'default_value' => 'Top rated',
                'name' => 'Top Rated Badge',
                'field_type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
               'key' => 'recommend_this',
                'default_value' => 'of users recommend',
                'name' => 'Recommendation Text',
                'field_type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'product_badge_label',
                'default_value' => 'Key Features',
                'name' => 'Product Features Badge',
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

        if (function_exists('cache')) {
            cache()->forget('static_texts_all');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('static_content_keys')->whereIn('key', [
            'recommend_this',
        ])->delete();

        DB::table('static_content_keys')->where('key', 'category_badge_label')->update([
            'default_value' => 'Top Choice',
            'name' => 'Category Badge Label',
        ]);

        if (function_exists('cache')) {
            cache()->forget('static_texts_all');
        }
    }
};
