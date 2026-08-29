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
            $table->unsignedBigInteger('top_rated_image')->nullable()->after('category_icon');
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->text('top_rated_listing_subtext')->nullable()->after('top_rated_listing_headline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('top_rated_image');
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn('top_rated_listing_subtext');
        });
    }
};
