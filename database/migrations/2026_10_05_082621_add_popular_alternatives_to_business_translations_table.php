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
            if (!Schema::hasColumn('business_translations', 'popular_alternatives_headline')) {
                $table->string('popular_alternatives_headline')->nullable()->after('alternatives_description');
            }
            if (!Schema::hasColumn('business_translations', 'popular_alternatives_subtext')) {
                $table->mediumText('popular_alternatives_subtext')->nullable()->after('popular_alternatives_headline');
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
            if (Schema::hasColumn('business_translations', 'popular_alternatives_headline')) {
                $columnsToDrop[] = 'popular_alternatives_headline';
            }
            if (Schema::hasColumn('business_translations', 'popular_alternatives_subtext')) {
                $columnsToDrop[] = 'popular_alternatives_subtext';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
