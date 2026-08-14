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
        if (!Schema::hasColumn('product_translations', 'is_affiliate')) {
            Schema::table('product_translations', function (Blueprint $table) {
            $table->boolean('is_affiliate')->default(false)->after('product_link');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('product_translations', 'is_affiliate')) {
            Schema::table('product_translations', function (Blueprint $table) {
            $table->dropColumn('is_affiliate');
        });
        }
    }
};
