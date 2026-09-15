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
        Schema::table('feature_translations', function (Blueprint $table) {
            $table->string('category_cap')->nullable()->after('name');
            $table->string('category_tc')->nullable()->after('category_cap');
            $table->string('category_lc')->nullable()->after('category_tc');
            $table->string('feature_cap')->nullable()->after('category_lc');
            $table->string('feature_tc')->nullable()->after('feature_cap');
            $table->string('feature_lc')->nullable()->after('feature_tc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_translations', function (Blueprint $table) {
            $table->dropColumn([
                'category_cap',
                'category_tc',
                'category_lc',
                'feature_cap',
                'feature_tc',
                'feature_lc',
            ]);
        });
    }
};
