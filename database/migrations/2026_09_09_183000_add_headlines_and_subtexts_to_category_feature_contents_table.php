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
        if (Schema::hasTable('category_feature_contents')) {
            Schema::table('category_feature_contents', function (Blueprint $table) {
                if (!Schema::hasColumn('category_feature_contents', 'page_title')) {
                    $table->string('page_title')->nullable()->after('lang_id');
                }
                if (!Schema::hasColumn('category_feature_contents', 'top_rated_listing_headline')) {
                    $table->string('top_rated_listing_headline')->nullable()->after('description');
                }
                if (!Schema::hasColumn('category_feature_contents', 'top_rated_listing_subtext')) {
                    $table->text('top_rated_listing_subtext')->nullable()->after('top_rated_listing_headline');
                }
                if (!Schema::hasColumn('category_feature_contents', 'compare_alternatives_headline')) {
                    $table->string('compare_alternatives_headline')->nullable()->after('top_rated_listing_subtext');
                }
                if (!Schema::hasColumn('category_feature_contents', 'compare_alternatives_subtext')) {
                    $table->text('compare_alternatives_subtext')->nullable()->after('compare_alternatives_headline');
                }
                if (!Schema::hasColumn('category_feature_contents', 'community_says_headline')) {
                    $table->string('community_says_headline')->nullable()->after('compare_alternatives_subtext');
                }
                if (!Schema::hasColumn('category_feature_contents', 'community_says_subtext')) {
                    $table->text('community_says_subtext')->nullable()->after('community_says_headline');
                }
                if (!Schema::hasColumn('category_feature_contents', 'explore_subcategories_headline')) {
                    $table->string('explore_subcategories_headline')->nullable()->after('community_says_subtext');
                }
                if (!Schema::hasColumn('category_feature_contents', 'explore_subcategories_subtext')) {
                    $table->text('explore_subcategories_subtext')->nullable()->after('explore_subcategories_headline');
                }
                if (!Schema::hasColumn('category_feature_contents', 'popular_comparisons_headline')) {
                    $table->string('popular_comparisons_headline')->nullable()->after('explore_subcategories_subtext');
                }
                if (!Schema::hasColumn('category_feature_contents', 'popular_comparisons_subtext')) {
                    $table->text('popular_comparisons_subtext')->nullable()->after('popular_comparisons_headline');
                }
                if (!Schema::hasColumn('category_feature_contents', 'meta_title')) {
                    $table->string('meta_title')->nullable()->after('popular_comparisons_subtext');
                }
                if (!Schema::hasColumn('category_feature_contents', 'meta_description')) {
                    $table->text('meta_description')->nullable()->after('meta_title');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('category_feature_contents')) {
            Schema::table('category_feature_contents', function (Blueprint $table) {
                $columns = [
                    'page_title',
                    'top_rated_listing_headline',
                    'top_rated_listing_subtext',
                    'compare_alternatives_headline',
                    'compare_alternatives_subtext',
                    'community_says_headline',
                    'community_says_subtext',
                    'explore_subcategories_headline',
                    'explore_subcategories_subtext',
                    'popular_comparisons_headline',
                    'popular_comparisons_subtext',
                    'meta_title',
                    'meta_description',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('category_feature_contents', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
