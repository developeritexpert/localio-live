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
        if (Schema::hasTable('feature_translations')) {
            Schema::table('feature_translations', function (Blueprint $table) {
                if (!Schema::hasColumn('feature_translations', 'feature_with_lc')) {
                    $table->string('feature_with_lc')->nullable()->after('feature_lc');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('feature_translations')) {
            Schema::table('feature_translations', function (Blueprint $table) {
                if (Schema::hasColumn('feature_translations', 'feature_with_lc')) {
                    $table->dropColumn('feature_with_lc');
                }
            });
        }
    }
};
