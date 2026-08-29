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
            $table->string('top_rated_listing_headline')->nullable()->after('top_rated_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn('top_rated_listing_headline');
        });
    }
};
