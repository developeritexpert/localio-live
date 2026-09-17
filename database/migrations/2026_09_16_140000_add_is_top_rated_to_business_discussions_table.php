<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('business_discussions')) {
            // Ensure business_id is nullable for category or top-rated discussions
            try {
                DB::statement('ALTER TABLE business_discussions MODIFY business_id BIGINT UNSIGNED NULL;');
            } catch (\Throwable $e) {
                // Ignore if already nullable or driver differences
            }

            if (!Schema::hasColumn('business_discussions', 'is_top_rated')) {
                Schema::table('business_discussions', function (Blueprint $table) {
                    $table->boolean('is_top_rated')->default(false)->index()->after('category_id');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('business_discussions') && Schema::hasColumn('business_discussions', 'is_top_rated')) {
            Schema::table('business_discussions', function (Blueprint $table) {
                $table->dropColumn('is_top_rated');
            });
        }
    }
};
