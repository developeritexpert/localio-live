<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('explore_subcategories_headline')->nullable()->after('community_says_subtext');
            $table->mediumText('explore_subcategories_subtext')->nullable()->after('explore_subcategories_headline');
        });
    }

    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn([
                'explore_subcategories_headline',
                'explore_subcategories_subtext'
            ]);
        });
    }
};
