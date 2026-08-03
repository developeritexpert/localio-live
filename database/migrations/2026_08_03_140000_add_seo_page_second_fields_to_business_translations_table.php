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
            if (!Schema::hasColumn('business_translations', 'alternatives_title_2')) {
                $table->string('alternatives_title_2')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'alternatives_description_2')) {
                $table->longText('alternatives_description_2')->nullable();
            }

            if (!Schema::hasColumn('business_translations', 'reviews_title_2')) {
                $table->string('reviews_title_2')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'reviews_description_2')) {
                $table->longText('reviews_description_2')->nullable();
            }

            if (!Schema::hasColumn('business_translations', 'faqs_title_2')) {
                $table->string('faqs_title_2')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'faqs_description_2')) {
                $table->longText('faqs_description_2')->nullable();
            }

            if (!Schema::hasColumn('business_translations', 'comparison_title_2')) {
                $table->string('comparison_title_2')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'comparison_description_2')) {
                $table->longText('comparison_description_2')->nullable();
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
                'alternatives_title_2',
                'alternatives_description_2',
                'reviews_title_2',
                'reviews_description_2',
                'faqs_title_2',
                'faqs_description_2',
                'comparison_title_2',
                'comparison_description_2',
            ]);
        });
    }
};
