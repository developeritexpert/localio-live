<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('category_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('category_translations', 'faq_categories')) {
                $table->json('faq_categories')->nullable()->after('faqs');
            }
        });
    }

    public function down()
    {
        Schema::table('category_translations', function (Blueprint $table) {
            if (Schema::hasColumn('category_translations', 'faq_categories')) {
                $table->dropColumn('faq_categories');
            }
        });
    }
};
