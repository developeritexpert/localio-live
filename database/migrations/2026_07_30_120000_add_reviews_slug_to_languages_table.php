<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('languages') && !Schema::hasColumn('languages', 'reviews_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('reviews_slug')->nullable()->default('reviews')->after('alternatives_slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('languages') && Schema::hasColumn('languages', 'reviews_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('reviews_slug');
            });
        }
    }
};
