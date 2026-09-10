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
                'key' => 'compare_search_placeholder',
                'default_value' => 'Search by name...',
                'name' => 'Compare Search Input Placeholder',
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
            'compare_search_placeholder',
        ])->delete();

        if (function_exists('cache')) {
            cache()->forget('static_texts_all');
        }
    }
};
