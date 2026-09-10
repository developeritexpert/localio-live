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
        Schema::table('category_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('category_translations', 'faq_explore_headline')) {
                $table->string('faq_explore_headline')->nullable()->after('faq_about_description');
            }
            if (!Schema::hasColumn('category_translations', 'faq_explore_subtext')) {
                $table->mediumText('faq_explore_subtext')->nullable()->after('faq_explore_headline');
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            if (Schema::hasColumn('category_translations', 'faq_explore_subtext')) {
                $table->dropColumn('faq_explore_subtext');
            }
            if (Schema::hasColumn('category_translations', 'faq_explore_headline')) {
                $table->dropColumn('faq_explore_headline');
            }
        });
    }
};