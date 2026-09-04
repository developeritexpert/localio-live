<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('compare_alternatives_headline')->nullable()->after('top_rated_listing_subtext');
            $table->string('community_says_headline')->nullable()->after('compare_alternatives_subtext');
        });
    }

    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn([
                'compare_alternatives_headline',
                'community_says_headline'
            ]);
        });
    }
};
