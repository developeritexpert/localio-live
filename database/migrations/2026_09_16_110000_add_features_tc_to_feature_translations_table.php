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
        Schema::table('feature_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('feature_translations', 'features_tc')) {
                $table->string('features_tc')->nullable()->after('feature_tc');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_translations', function (Blueprint $table) {
            if (Schema::hasColumn('feature_translations', 'features_tc')) {
                $table->dropColumn('features_tc');
            }
        });
    }
};
