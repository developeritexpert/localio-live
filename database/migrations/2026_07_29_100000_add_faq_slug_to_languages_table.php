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
        if (Schema::hasTable('languages') && !Schema::hasColumn('languages', 'faq_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('faq_slug')->nullable()->default('faqs')->after('lang_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('languages') && Schema::hasColumn('languages', 'faq_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('faq_slug');
            });
        }
    }
};
