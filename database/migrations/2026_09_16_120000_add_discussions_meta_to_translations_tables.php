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
            if (!Schema::hasColumn('business_translations', 'discussions_meta_title')) {
                $table->string('discussions_meta_title', 255)->nullable()->after('comparison_meta_description');
            }
            if (!Schema::hasColumn('business_translations', 'discussions_meta_description')) {
                $table->text('discussions_meta_description')->nullable()->after('discussions_meta_title');
            }
        });

        Schema::table('category_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('category_translations', 'discussions_meta_title')) {
                $table->string('discussions_meta_title', 255)->nullable()->after('faq_meta_description');
            }
            if (!Schema::hasColumn('category_translations', 'discussions_meta_description')) {
                $table->text('discussions_meta_description')->nullable()->after('discussions_meta_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_translations', function (Blueprint $table) {
            if (Schema::hasColumn('business_translations', 'discussions_meta_title')) {
                $table->dropColumn('discussions_meta_title');
            }
            if (Schema::hasColumn('business_translations', 'discussions_meta_description')) {
                $table->dropColumn('discussions_meta_description');
            }
        });

        Schema::table('category_translations', function (Blueprint $table) {
            if (Schema::hasColumn('category_translations', 'discussions_meta_title')) {
                $table->dropColumn('discussions_meta_title');
            }
            if (Schema::hasColumn('category_translations', 'discussions_meta_description')) {
                $table->dropColumn('discussions_meta_description');
            }
        });
    }
};
