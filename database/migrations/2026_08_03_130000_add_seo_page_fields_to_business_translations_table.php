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
            if (!Schema::hasColumn('business_translations', 'alternatives_title')) {
                $table->string('alternatives_title')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'alternatives_description')) {
                $table->longText('alternatives_description')->nullable();
            }

            if (!Schema::hasColumn('business_translations', 'reviews_title')) {
                $table->string('reviews_title')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'reviews_description')) {
                $table->longText('reviews_description')->nullable();
            }

            if (!Schema::hasColumn('business_translations', 'faqs_title')) {
                $table->string('faqs_title')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'faqs_description')) {
                $table->longText('faqs_description')->nullable();
            }

            if (!Schema::hasColumn('business_translations', 'comparison_title')) {
                $table->string('comparison_title')->nullable();
            }
            if (!Schema::hasColumn('business_translations', 'comparison_description')) {
                $table->longText('comparison_description')->nullable();
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
                'alternatives_title',
                'alternatives_description',
                'reviews_title',
                'reviews_description',
                'faqs_title',
                'faqs_description',
                'comparison_title',
                'comparison_description',
            ]);
        });
    }
};
