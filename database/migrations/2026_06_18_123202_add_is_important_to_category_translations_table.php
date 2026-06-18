<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->tinyInteger('is_important')
                  ->default(0)
                  ->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn('is_important');
        });
    }
};