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
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('faq_image')->nullable()->after('top_rated_image');
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('faq_h1_title')->nullable()->after('top_rated_listing_subtext');
            $table->text('faq_subtext')->nullable()->after('faq_h1_title');
            $table->string('faq_about_headline')->nullable()->after('faq_subtext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('faq_image');
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn(['faq_h1_title', 'faq_subtext', 'faq_about_headline']);
        });
    }
};