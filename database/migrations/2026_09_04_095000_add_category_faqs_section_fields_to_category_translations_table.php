<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('category_faqs_headline')->nullable()->after('explore_subcategories_subtext');
            $table->mediumText('category_faqs_subtext')->nullable()->after('category_faqs_headline');
        });
    }

    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn([
                'category_faqs_headline',
                'category_faqs_subtext'
            ]);
        });
    }
};
