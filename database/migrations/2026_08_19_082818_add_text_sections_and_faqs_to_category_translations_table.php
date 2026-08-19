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
        Schema::table('category_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('category_translations', 'text_sections')) {
                $table->longText('text_sections')->nullable()->after('description');
            }
            if (!Schema::hasColumn('category_translations', 'faqs')) {
                $table->longText('faqs')->nullable()->after('text_sections');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            if (Schema::hasColumn('category_translations', 'text_sections')) {
                $table->dropColumn('text_sections');
            }
            if (Schema::hasColumn('category_translations', 'faqs')) {
                $table->dropColumn('faqs');
            }
        });
    }
};
