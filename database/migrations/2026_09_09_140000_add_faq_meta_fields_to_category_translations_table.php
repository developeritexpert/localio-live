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
            if (!Schema::hasColumn('category_translations', 'faq_meta_title')) {
                $table->string('faq_meta_title')->nullable()->after('faq_about_description');
            }
            if (!Schema::hasColumn('category_translations', 'faq_meta_description')) {
                $table->text('faq_meta_description')->nullable()->after('faq_meta_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('category_translations', 'faq_meta_title')) {
                $columnsToDrop[] = 'faq_meta_title';
            }
            if (Schema::hasColumn('category_translations', 'faq_meta_description')) {
                $columnsToDrop[] = 'faq_meta_description';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
