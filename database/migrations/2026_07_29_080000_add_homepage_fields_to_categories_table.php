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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'show_on_homepage')) {
                $table->boolean('show_on_homepage')->default(0)->after('status');
            }
            if (!Schema::hasColumn('categories', 'homepage_order')) {
                $table->integer('homepage_order')->default(0)->after('show_on_homepage');
            }
            if (!Schema::hasColumn('categories', 'homepage_product_limit')) {
                $table->integer('homepage_product_limit')->nullable()->default(6)->after('homepage_order');
            }
        });

        Schema::table('category_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('category_translations', 'homepage_link_text')) {
                $table->string('homepage_link_text')->nullable()->after('title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['show_on_homepage', 'homepage_order', 'homepage_product_limit']);
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn(['homepage_link_text']);
        });
    }
};
