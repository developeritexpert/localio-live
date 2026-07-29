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
        if (Schema::hasTable('languages') && !Schema::hasColumn('languages', 'alternatives_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('alternatives_slug')->nullable()->default('alternatives')->after('faq_slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('languages') && Schema::hasColumn('languages', 'alternatives_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('alternatives_slug');
            });
        }
    }
};
