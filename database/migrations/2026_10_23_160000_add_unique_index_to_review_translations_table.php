<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('review_translations')) {
            // Remove any potential duplicate records keeping the latest updated one
            DB::statement("
                DELETE t1 FROM review_translations t1
                INNER JOIN review_translations t2 
                WHERE t1.id < t2.id 
                  AND t1.reviews_id = t2.reviews_id 
                  AND t1.target_locale = t2.target_locale 
                  AND t1.target_locale IS NOT NULL
            ");

            // Check if index exists via MySQL information_schema
            $existing = DB::select("
                SHOW INDEX FROM review_translations 
                WHERE Key_name = 'review_translations_unique_review_target'
            ");

            if (empty($existing)) {
                Schema::table('review_translations', function (Blueprint $table) {
                    $table->unique(['reviews_id', 'target_locale'], 'review_translations_unique_review_target');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('review_translations')) {
            $existing = DB::select("
                SHOW INDEX FROM review_translations 
                WHERE Key_name = 'review_translations_unique_review_target'
            ");

            if (!empty($existing)) {
                Schema::table('review_translations', function (Blueprint $table) {
                    $table->dropUnique('review_translations_unique_review_target');
                });
            }
        }
    }
};
