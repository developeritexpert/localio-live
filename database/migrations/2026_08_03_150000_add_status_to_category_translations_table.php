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
        if (!Schema::hasColumn('category_translations', 'status')) {
            Schema::table('category_translations', function (Blueprint $table) {
                $table->tinyInteger('status')->default(1)->after('lang_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('category_translations', 'status')) {
            Schema::table('category_translations', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
