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
            if (!Schema::hasColumn('category_translations', 'see_all_text')) {
                $table->string('see_all_text')->nullable()->after('comparison_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            if (Schema::hasColumn('category_translations', 'see_all_text')) {
                $table->dropColumn('see_all_text');
            }
        });
    }
};
