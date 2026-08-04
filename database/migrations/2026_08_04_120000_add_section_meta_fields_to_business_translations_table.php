<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('business_translations', 'alternatives_meta_title')) {
                $table->text('alternatives_meta_title')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'alternatives_meta_description')) {
                $table->text('alternatives_meta_description')->nullable();
            }

            if (!Schema::hasColumn('business_translations', 'reviews_meta_title')) {
                $table->text('reviews_meta_title')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'reviews_meta_description')) {
                $table->text('reviews_meta_description')->nullable();
            }

            if (!Schema::hasColumn('business_translations', 'faqs_meta_title')) {
                $table->text('faqs_meta_title')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'faqs_meta_description')) {
                $table->text('faqs_meta_description')->nullable();
            }

            if (!Schema::hasColumn('business_translations', 'comparison_meta_title')) {
                $table->text('comparison_meta_title')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'comparison_meta_description')) {
                $table->text('comparison_meta_description')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_translations', function (Blueprint $table) {
            $table->dropColumn([
                'alternatives_meta_title',
                'alternatives_meta_description',
                'reviews_meta_title',
                'reviews_meta_description',
                'faqs_meta_title',
                'faqs_meta_description',
                'comparison_meta_title',
                'comparison_meta_description',
            ]);
        });
    }
};
