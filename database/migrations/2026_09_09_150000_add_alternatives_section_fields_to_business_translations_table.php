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
            if (!Schema::hasColumn('business_translations', 'compare_alternatives_headline')) {
                $table->string('compare_alternatives_headline')->nullable()->after('alternatives_description');
            }
            if (!Schema::hasColumn('business_translations', 'compare_alternatives_subtext')) {
                $table->mediumText('compare_alternatives_subtext')->nullable()->after('compare_alternatives_headline');
            }
            if (!Schema::hasColumn('business_translations', 'community_says_headline')) {
                $table->string('community_says_headline')->nullable()->after('compare_alternatives_subtext');
            }
            if (!Schema::hasColumn('business_translations', 'community_says_subtext')) {
                $table->mediumText('community_says_subtext')->nullable()->after('community_says_headline');
            }
            if (!Schema::hasColumn('business_translations', 'all_alternatives_headline')) {
                $table->string('all_alternatives_headline')->nullable()->after('community_says_subtext');
            }
            if (!Schema::hasColumn('business_translations', 'all_alternatives_subtext')) {
                $table->mediumText('all_alternatives_subtext')->nullable()->after('all_alternatives_headline');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_translations', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach ([
                'compare_alternatives_headline',
                'compare_alternatives_subtext',
                'community_says_headline',
                'community_says_subtext',
                'all_alternatives_headline',
                'all_alternatives_subtext',
            ] as $col) {
                if (Schema::hasColumn('business_translations', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
