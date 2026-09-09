<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void	{
        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('compare_tool_headline')->nullable()->after('popular_comparisons_subtext');
            $table->mediumText('compare_tool_subtext')->nullable()->after('compare_tool_headline');
        });
    }

    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn([
                'compare_tool_headline',
                'compare_tool_subtext'
            ]);
        });
    }
};
