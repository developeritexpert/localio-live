<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('languages') && !Schema::hasColumn('languages', 'comparisons_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('comparisons_slug')->nullable()->default('comparisons')->after('reviews_slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('languages') && Schema::hasColumn('languages', 'comparisons_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('comparisons_slug');
            });
        }
    }
};
