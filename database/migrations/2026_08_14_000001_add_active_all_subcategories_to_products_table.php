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
        if (!Schema::hasColumn('products', 'active_all_subcategories')) {
            Schema::table('products', function (Blueprint $table) {
                $table->tinyInteger('active_all_subcategories')->default(1)->after('active_all_countries');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'active_all_subcategories')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('active_all_subcategories');
            });
        }
    }
};
